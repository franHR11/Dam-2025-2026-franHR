<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
        exit;
    }

    $db = facturacion_db();
    $db->beginTransaction();

    try {
        // Validar y normalizar datos principales
        $id = isset($data['id']) ? (int) $data['id'] : null;
        $tipoFactura = $data['tipo_factura'] ?? 'venta';
        $serie = strtoupper(trim($data['numero_serie'] ?? 'FAC'));
        $ejercicio = (int) ($data['ejercicio'] ?? date('Y'));
        $fecha = factura_normalizar_fecha($data['fecha'] ?? null);
        $fechaVencimiento = factura_normalizar_fecha($data['fecha_vencimiento'] ?? null);
        $estado = $data['estado'] ?? 'borrador';
        $moneda = strtoupper(trim($data['moneda'] ?? 'EUR'));
        $formaPago = $data['forma_pago'] ?? 'transferencia';
        $numeroCuenta = trim($data['numero_cuenta'] ?? '');
        $terminosCondiciones = trim($data['terminos_condiciones'] ?? '');
        $notasInternas = trim($data['notas_internas'] ?? '');

        // Validar tercero según tipo
        $clienteId = null;
        $proveedorId = null;

        if ($tipoFactura === 'compra') {
            $proveedorId = isset($data['proveedor_id']) ? (int) $data['proveedor_id'] : null;
            factura_validar_proveedor($db, $proveedorId);
        } else {
            $clienteId = isset($data['cliente_id']) ? (int) $data['cliente_id'] : null;
            factura_validar_cliente($db, $clienteId);
        }

        // Validaciones específicas por tipo
        $facturaRectificadaId = null;
        $motivoRectificacion = null;

        if ($tipoFactura === 'rectificativa') {
            $facturaRectificadaId = isset($data['factura_rectificada_id']) ? (int) $data['factura_rectificada_id'] : null;
            $motivoRectificacion = trim($data['motivo_rectificacion'] ?? '');

            if (!$facturaRectificadaId) {
                throw new InvalidArgumentException('Las facturas rectificativas deben indicar la factura original');
            }
        }

        // Datos de pago
        $fechaPago = null;
        $importePagado = 0;
        $metodoPago = null;
        $referenciaPago = null;

        if (!empty($data['fecha_pago'])) {
            $fechaPago = $data['fecha_pago'];
            $importePagado = (float) ($data['importe_pagado'] ?? 0);
            $metodoPago = trim($data['metodo_pago'] ?? '');
            $referenciaPago = trim($data['referencia_pago'] ?? '');
        }

        // Validar y normalizar líneas
        $lineas = factura_normalizar_lineas($data['lineas'] ?? []);
        $totales = factura_calcular_totales($lineas);

        // Generar número si es nueva factura
        $numeroFactura = $data['numero_factura'] ?? null;
        if (!$id && !$numeroFactura) {
            $numeroFactura = factura_generar_numero($db, $ejercicio, $serie);
        }

        // Preparar datos para inserción/actualización
        $facturaData = [
            'numero_factura' => $numeroFactura,
            'numero_serie' => $serie,
            'ejercicio' => $ejercicio,
            'fecha' => $fecha,
            'fecha_vencimiento' => $fechaVencimiento,
            'tipo_factura' => $tipoFactura,
            'estado' => $estado,
            'cliente_id' => $clienteId,
            'proveedor_id' => $proveedorId,
            'direccion_envio_id' => $data['direccion_envio_id'] ?? null,
            'contacto_id' => $data['contacto_id'] ?? null,
            'presupuesto_id' => $data['presupuesto_id'] ?? null,
            'base_imponible' => $totales['base'],
            'importe_descuento' => $totales['descuento'],
            'porcentaje_descuento' => 0, // Calcular si es necesario
            'base_imponible_descuento' => $totales['base_descuento'],
            'importe_iva' => $totales['iva'],
            'importe_irpf' => $totales['irpf'],
            'retencion_irpf' => 0, // Calcular si es necesario
            'total' => $totales['total'],
            'moneda' => $moneda,
            'forma_pago' => $formaPago,
            'numero_cuenta' => $numeroCuenta ?: null,
            'notas_internas' => $notasInternas ?: null,
            'terminos_condiciones' => $terminosCondiciones ?: null,
            'fecha_pago' => $fechaPago,
            'importe_pagado' => $importePagado,
            'metodo_pago' => $metodoPago ?: null,
            'referencia_pago' => $referenciaPago ?: null,
            'factura_rectificada_id' => $facturaRectificadaId,
            'motivo_rectificacion' => $motivoRectificacion ?: null,
            'enviada_email' => 0,
            'created_by' => factura_usuario_actual()
        ];

        if ($id) {
            // Actualizar factura existente
            $facturaData['updated_at'] = date('Y-m-d H:i:s');

            $sql = "UPDATE facturas SET ";
            $sets = [];
            $params = [];

            foreach ($facturaData as $campo => $valor) {
                $sets[] = "$campo = ?";
                $params[] = $valor;
            }
            $params[] = $id;

            $sql .= implode(', ', $sets) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $facturaId = $id;
        } else {
            // Insertar nueva factura
            $facturaData['created_at'] = date('Y-m-d H:i:s');
            $facturaData['updated_at'] = date('Y-m-d H:i:s');

            $campos = implode(', ', array_keys($facturaData));
            $placeholders = implode(', ', array_fill(0, count($facturaData), '?'));

            $sql = "INSERT INTO facturas ($campos) VALUES ($placeholders)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($facturaData));

            $facturaId = $db->lastInsertId();
        }

        // Guardar líneas
        factura_guardar_lineas($db, $facturaId, $lineas);

        // Actualizar estado si es pago total
        if ($importePagado >= $totales['total'] && $importePagado > 0) {
            $estadoFinal = ($tipoFactura === 'compra') ? 'pagada' : 'cobrada';
            $stmt = $db->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
            $stmt->execute([$estadoFinal, $facturaId]);
        }

        $db->commit();

        // Obtener factura completa para respuesta
        $facturaCompleta = factura_obtener($db, $facturaId);

        echo json_encode([
            'ok' => true,
            'message' => $id ? 'Factura actualizada correctamente' : 'Factura creada correctamente',
            'factura' => $facturaCompleta,
            'id' => $facturaId
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}