<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function facturacion_db(): PDO
{
    $db = getConnection();
    if (!$db) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    return $db;
}

function factura_generar_numero(PDO $db, int $ejercicio, string $serie = 'FAC'): string
{
    $serie = strtoupper(trim($serie)) ?: 'FAC';
    $prefijo = $serie . '-' . $ejercicio . '-';
    $stmt = $db->prepare("SELECT numero_factura FROM facturas WHERE ejercicio = ? AND numero_factura LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$ejercicio, $prefijo . '%']);
    $ultimo = $stmt->fetchColumn();
    if (!$ultimo) {
        return $prefijo . '0001';
    }
    $correlativo = (int) substr($ultimo, -4) + 1;
    return sprintf('%s%04d', $prefijo, $correlativo);
}

function factura_normalizar_fecha(?string $fecha): string
{
    if (!$fecha) {
        throw new InvalidArgumentException('La fecha es obligatoria');
    }
    $fechaObj = DateTime::createFromFormat('Y-m-d', substr($fecha, 0, 10));
    if (!$fechaObj) {
        throw new InvalidArgumentException('Formato de fecha inválido');
    }
    return $fechaObj->format('Y-m-d');
}

function factura_normalizar_lineas(array $lineas): array
{
    if (empty($lineas)) {
        throw new InvalidArgumentException('Debes incluir al menos una línea en la factura');
    }
    $resultado = [];
    foreach ($lineas as $index => $linea) {
        $descripcion = trim((string) ($linea['descripcion'] ?? ''));
        if ($descripcion === '') {
            throw new InvalidArgumentException('La descripción de cada línea es obligatoria');
        }
        $cantidad = max(0, (float) ($linea['cantidad'] ?? 1));
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que 0');
        }
        $precioUnitario = max(0, (float) ($linea['precio_unitario'] ?? 0));
        $descuentoLinea = max(0, min(100, (float) ($linea['descuento_linea'] ?? 0)));
        $ivaTipo = strtoupper(trim((string) ($linea['iva_tipo'] ?? '21')));
        $ivaValor = $ivaTipo === 'EXENTO' ? 0 : (float) $ivaTipo;
        $irpfTipo = isset($linea['irpf_tipo']) ? strtoupper(trim((string) $linea['irpf_tipo'])) : null;
        $irpfValor = $irpfTipo ? (float) $irpfTipo : 0;

        $subtotal = round($cantidad * $precioUnitario, 2);
        $importeDescuento = round($subtotal * ($descuentoLinea / 100), 2);
        $base = round($subtotal - $importeDescuento, 2);
        $importeIva = round($base * ($ivaValor / 100), 2);
        $importeIrpf = round($base * ($irpfValor / 100), 2);
        $totalLinea = round($base + $importeIva - $importeIrpf, 2);

        $resultado[] = [
            'linea' => $index + 1,
            'producto_id' => isset($linea['producto_id']) && $linea['producto_id'] !== '' ? (int) $linea['producto_id'] : null,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $linea['unidad_medida'] ?? 'unidades',
            'precio_unitario' => $precioUnitario,
            'descuento_linea' => $descuentoLinea,
            'importe_descuento' => $importeDescuento,
            'subtotal' => $subtotal,
            'iva_tipo' => $ivaTipo,
            'importe_iva' => $importeIva,
            'irpf_tipo' => $irpfTipo,
            'importe_irpf' => $importeIrpf,
            'total_linea' => $totalLinea,
        ];
    }
    return $resultado;
}

function factura_calcular_totales(array $lineas): array
{
    $totales = ['base' => 0, 'descuento' => 0, 'iva' => 0, 'irpf' => 0, 'total' => 0];
    foreach ($lineas as $linea) {
        $totales['base'] += $linea['subtotal'];
        $totales['descuento'] += $linea['importe_descuento'];
        $totales['iva'] += $linea['importe_iva'];
        $totales['irpf'] += $linea['importe_irpf'];
        $totales['total'] += $linea['total_linea'];
    }
    $totales['base_descuento'] = round($totales['base'] - $totales['descuento'], 2);
    foreach ($totales as $key => $value) {
        $totales[$key] = round($value, 2);
    }
    return $totales;
}

function factura_guardar_lineas(PDO $db, int $facturaId, array $lineas): void
{
    $db->prepare('DELETE FROM facturas_lineas WHERE factura_id = ?')->execute([$facturaId]);
    $sql = 'INSERT INTO facturas_lineas (
        factura_id, linea, producto_id, descripcion, cantidad, unidad_medida,
        precio_unitario, descuento_linea, importe_descuento, subtotal,
        iva_tipo, importe_iva, irpf_tipo, importe_irpf, total_linea,
        created_at, updated_at
    ) VALUES (
        :factura_id, :linea, :producto_id, :descripcion, :cantidad, :unidad_medida,
        :precio_unitario, :descuento_linea, :importe_descuento, :subtotal,
        :iva_tipo, :importe_iva, :irpf_tipo, :importe_irpf, :total_linea,
        NOW(), NOW()
    )';
    $stmt = $db->prepare($sql);
    foreach ($lineas as $linea) {
        $stmt->execute([
            ':factura_id' => $facturaId,
            ':linea' => $linea['linea'],
            ':producto_id' => $linea['producto_id'],
            ':descripcion' => $linea['descripcion'],
            ':cantidad' => $linea['cantidad'],
            ':unidad_medida' => $linea['unidad_medida'],
            ':precio_unitario' => $linea['precio_unitario'],
            ':descuento_linea' => $linea['descuento_linea'],
            ':importe_descuento' => $linea['importe_descuento'],
            ':subtotal' => $linea['subtotal'],
            ':iva_tipo' => $linea['iva_tipo'],
            ':importe_iva' => $linea['importe_iva'],
            ':irpf_tipo' => $linea['irpf_tipo'],
            ':importe_irpf' => $linea['importe_irpf'],
            ':total_linea' => $linea['total_linea'],
        ]);
    }
}

function factura_obtener(PDO $db, int $id)
{
    $stmt = $db->prepare('SELECT f.*, c.nombre_comercial AS cliente_nombre, p.nombre_comercial AS proveedor_nombre
        FROM facturas f
        LEFT JOIN clientes c ON c.id = f.cliente_id
        LEFT JOIN proveedores p ON p.id = f.proveedor_id
        WHERE f.id = ?');
    $stmt->execute([$id]);
    $factura = $stmt->fetch();
    if (!$factura) {
        return null;
    }
    $stmtLineas = $db->prepare('SELECT * FROM facturas_lineas WHERE factura_id = ? ORDER BY linea ASC');
    $stmtLineas->execute([$id]);
    $factura['lineas'] = $stmtLineas->fetchAll();
    return $factura;
}

function factura_validar_cliente(PDO $db, ?int $clienteId): void
{
    if (!$clienteId) {
        return;
    }
    $stmt = $db->prepare('SELECT id FROM clientes WHERE id = ? AND activo = 1');
    $stmt->execute([$clienteId]);
    if (!$stmt->fetchColumn()) {
        throw new InvalidArgumentException('El cliente seleccionado no existe o está inactivo');
    }
}

function factura_validar_proveedor(PDO $db, ?int $proveedorId): void
{
    if (!$proveedorId) {
        return;
    }
    $stmt = $db->prepare('SELECT id FROM proveedores WHERE id = ? AND activo = 1');
    $stmt->execute([$proveedorId]);
    if (!$stmt->fetchColumn()) {
        throw new InvalidArgumentException('El proveedor seleccionado no existe o está inactivo');
    }
}

function factura_usuario_actual(): int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1;
}
