<?php
require_once __DIR__ . '/presupuestos_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        throw new InvalidArgumentException('No se recibieron datos válidos');
    }

    $db = presupuestos_db();
    $db->beginTransaction();

    $clienteId = isset($payload['cliente_id']) ? (int) $payload['cliente_id'] : 0;
    if ($clienteId <= 0) {
        throw new InvalidArgumentException('Debe seleccionar un cliente');
    }
    presupuesto_validar_cliente($db, $clienteId);

    $lineas = presupuesto_normalizar_lineas($payload['lineas'] ?? []);
    $totales = presupuesto_calcular_totales($lineas);

    $fecha = presupuesto_normalizar_fecha($payload['fecha'] ?? date('Y-m-d'));
    $fechaValido = isset($payload['fecha_valido_hasta']) && $payload['fecha_valido_hasta'] !== ''
        ? presupuesto_normalizar_fecha($payload['fecha_valido_hasta'])
        : date('Y-m-d', strtotime($fecha . ' +30 days'));

    $ejercicio = (int) ($payload['ejercicio'] ?? date('Y'));
    $estado = $payload['estado'] ?? 'borrador';
    $formaPago = $payload['forma_pago'] ?? 'transferencia';
    $moneda = $payload['moneda'] ?? 'EUR';
    $porcentajeDescuento = max(0, min(100, (float) ($payload['porcentaje_descuento'] ?? 0)));

    $usuarioId = presupuesto_usuario_actual();
    $ahora = date('Y-m-d H:i:s');

    $datos = [
        ':ejercicio' => $ejercicio,
        ':fecha' => $fecha,
        ':fecha_valido_hasta' => $fechaValido,
        ':estado' => $estado,
        ':cliente_id' => $clienteId,
        ':direccion_envio_id' => $payload['direccion_envio_id'] ?? null,
        ':contacto_id' => $payload['contacto_id'] ?? null,
        ':base_imponible' => $totales['base'],
        ':importe_descuento' => $totales['descuento'],
        ':porcentaje_descuento' => $porcentajeDescuento,
        ':base_imponible_descuento' => $totales['base_descuento'],
        ':importe_iva' => $totales['iva'],
        ':total' => $totales['total'],
        ':moneda' => $moneda,
        ':forma_pago' => $formaPago,
        ':plazo_entrega' => $payload['plazo_entrega'] ?? null,
        ':garantia' => $payload['garantia'] ?? null,
        ':notas_internas' => $payload['notas_internas'] ?? null,
        ':terminos_condiciones' => $payload['terminos_condiciones'] ?? null,
        ':aceptado_por' => $payload['aceptado_por'] ?? null,
        ':fecha_aceptacion' => $payload['fecha_aceptacion'] ?? null,
        ':motivo_rechazo' => $payload['motivo_rechazo'] ?? null,
        ':enviado_email' => !empty($payload['enviado_email']) ? 1 : 0,
        ':fecha_envio_email' => $payload['fecha_envio_email'] ?? null,
    ];

    $presupuestoId = isset($payload['id']) ? (int) $payload['id'] : 0;
    $numero = trim((string) ($payload['numero_presupuesto'] ?? ''));

    if ($presupuestoId > 0) {
        $stmtExiste = $db->prepare('SELECT numero_presupuesto FROM presupuestos WHERE id = ?');
        $stmtExiste->execute([$presupuestoId]);
        $presupuestoActual = $stmtExiste->fetch();
        if (!$presupuestoActual) {
            throw new InvalidArgumentException('El presupuesto indicado no existe');
        }
        if ($numero === '') {
            $numero = $presupuestoActual['numero_presupuesto'];
        }

        $sqlUpdate = 'UPDATE presupuestos SET
            numero_presupuesto = :numero_presupuesto,
            ejercicio = :ejercicio,
            fecha = :fecha,
            fecha_valido_hasta = :fecha_valido_hasta,
            estado = :estado,
            cliente_id = :cliente_id,
            direccion_envio_id = :direccion_envio_id,
            contacto_id = :contacto_id,
            base_imponible = :base_imponible,
            importe_descuento = :importe_descuento,
            porcentaje_descuento = :porcentaje_descuento,
            base_imponible_descuento = :base_imponible_descuento,
            importe_iva = :importe_iva,
            total = :total,
            moneda = :moneda,
            forma_pago = :forma_pago,
            plazo_entrega = :plazo_entrega,
            garantia = :garantia,
            notas_internas = :notas_internas,
            terminos_condiciones = :terminos_condiciones,
            aceptado_por = :aceptado_por,
            fecha_aceptacion = :fecha_aceptacion,
            motivo_rechazo = :motivo_rechazo,
            enviado_email = :enviado_email,
            fecha_envio_email = :fecha_envio_email,
            updated_at = :updated_at
            WHERE id = :id';

        $stmt = $db->prepare($sqlUpdate);
        $stmt->execute($datos + [
            ':numero_presupuesto' => $numero,
            ':updated_at' => $ahora,
            ':id' => $presupuestoId,
        ]);
    } else {
        if ($numero === '') {
            $numero = presupuesto_generar_numero($db, $ejercicio);
        }

        $sqlInsert = 'INSERT INTO presupuestos (
            numero_presupuesto, ejercicio, fecha, fecha_valido_hasta, estado,
            cliente_id, direccion_envio_id, contacto_id,
            base_imponible, importe_descuento, porcentaje_descuento, base_imponible_descuento,
            importe_iva, total, moneda, forma_pago,
            plazo_entrega, garantia, notas_internas, terminos_condiciones,
            aceptado_por, fecha_aceptacion, motivo_rechazo,
            enviado_email, fecha_envio_email, created_by, created_at, updated_at
        ) VALUES (
            :numero_presupuesto, :ejercicio, :fecha, :fecha_valido_hasta, :estado,
            :cliente_id, :direccion_envio_id, :contacto_id,
            :base_imponible, :importe_descuento, :porcentaje_descuento, :base_imponible_descuento,
            :importe_iva, :total, :moneda, :forma_pago,
            :plazo_entrega, :garantia, :notas_internas, :terminos_condiciones,
            :aceptado_por, :fecha_aceptacion, :motivo_rechazo,
            :enviado_email, :fecha_envio_email, :created_by, :created_at, :updated_at
        )';

        $stmt = $db->prepare($sqlInsert);
        $stmt->execute($datos + [
            ':numero_presupuesto' => $numero,
            ':created_by' => $usuarioId,
            ':created_at' => $ahora,
            ':updated_at' => $ahora,
        ]);

        $presupuestoId = (int) $db->lastInsertId();
    }

    presupuesto_guardar_lineas($db, $presupuestoId, $lineas);

    $db->commit();

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Presupuesto guardado correctamente',
        'presupuesto_id' => $presupuestoId,
        'numero_presupuesto' => $numero,
    ]);
} catch (InvalidArgumentException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
