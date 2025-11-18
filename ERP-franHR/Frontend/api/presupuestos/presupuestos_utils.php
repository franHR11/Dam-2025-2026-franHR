<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function presupuestos_db()
{
    $db = getConnection();
    if (!$db) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    return $db;
}

function presupuesto_generar_numero(PDO $db, int $ejercicio): string
{
    $prefijo = 'PRE-' . $ejercicio . '-';
    $stmt = $db->prepare("SELECT numero_presupuesto FROM presupuestos WHERE ejercicio = ? AND numero_presupuesto LIKE ? ORDER BY id DESC LIMIT 1");
    $like = $prefijo . '%';
    $stmt->execute([$ejercicio, $like]);
    $ultimo = $stmt->fetchColumn();

    if (!$ultimo) {
        return $prefijo . '0001';
    }

    $numero = (int) substr($ultimo, -4);
    $numero++;
    return sprintf('%s%04d', $prefijo, $numero);
}

function presupuesto_normalizar_fecha(?string $fecha): string
{
    if (!$fecha) {
        throw new InvalidArgumentException('La fecha del presupuesto es obligatoria');
    }
    $fechaObj = DateTime::createFromFormat('Y-m-d', substr($fecha, 0, 10));
    if (!$fechaObj) {
        throw new InvalidArgumentException('Formato de fecha inválido. Use YYYY-MM-DD');
    }
    return $fechaObj->format('Y-m-d');
}

function presupuesto_normalizar_lineas(array $lineas): array
{
    if (empty($lineas)) {
        throw new InvalidArgumentException('Debe proporcionar al menos una línea de presupuesto');
    }

    $resultado = [];
    foreach ($lineas as $index => $linea) {
        $descripcion = trim((string) ($linea['descripcion'] ?? ''));
        if ($descripcion === '') {
            throw new InvalidArgumentException("La descripción de la línea " . ($index + 1) . " es obligatoria");
        }

        $cantidad = max(0, (float) ($linea['cantidad'] ?? 1));
        if ($cantidad <= 0) {
            throw new InvalidArgumentException("La cantidad de la línea " . ($index + 1) . " debe ser mayor que 0");
        }

        $precioUnitario = max(0, (float) ($linea['precio_unitario'] ?? 0));
        $descuentoLinea = max(0, min(100, (float) ($linea['descuento_linea'] ?? 0)));
        $ivaTipo = strtoupper(trim((string) ($linea['iva_tipo'] ?? '21')));
        if ($ivaTipo === 'EXENTO') {
            $ivaValor = 0;
        } else {
            $ivaValor = (float) $ivaTipo;
        }

        $subtotalSinDesc = round($cantidad * $precioUnitario, 2);
        $importeDescuento = round($subtotalSinDesc * ($descuentoLinea / 100), 2);
        $baseLinea = round($subtotalSinDesc - $importeDescuento, 2);
        $importeIva = round($baseLinea * ($ivaValor / 100), 2);
        $totalLinea = round($baseLinea + $importeIva, 2);

        $resultado[] = [
            'linea' => $index + 1,
            'producto_id' => isset($linea['producto_id']) && $linea['producto_id'] !== '' ? (int) $linea['producto_id'] : null,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $linea['unidad_medida'] ?? 'unidades',
            'precio_unitario' => $precioUnitario,
            'descuento_linea' => $descuentoLinea,
            'importe_descuento' => $importeDescuento,
            'subtotal' => $subtotalSinDesc,
            'iva_tipo' => $ivaTipo,
            'importe_iva' => $importeIva,
            'total_linea' => $totalLinea,
        ];
    }

    return $resultado;
}

function presupuesto_calcular_totales(array $lineas): array
{
    $base = 0;
    $descuento = 0;
    $iva = 0;
    $total = 0;

    foreach ($lineas as $linea) {
        $base += $linea['subtotal'];
        $descuento += $linea['importe_descuento'];
        $iva += $linea['importe_iva'];
        $total += $linea['total_linea'];
    }

    $baseDescuento = round($base - $descuento, 2);

    return [
        'base' => round($base, 2),
        'descuento' => round($descuento, 2),
        'base_descuento' => $baseDescuento,
        'iva' => round($iva, 2),
        'total' => round($total, 2),
    ];
}

function presupuesto_guardar_lineas(PDO $db, int $presupuestoId, array $lineas): void
{
    $stmtDelete = $db->prepare('DELETE FROM presupuestos_lineas WHERE presupuesto_id = ?');
    $stmtDelete->execute([$presupuestoId]);

    $sql = 'INSERT INTO presupuestos_lineas (
        presupuesto_id, linea, producto_id, descripcion, cantidad, unidad_medida, precio_unitario,
        descuento_linea, importe_descuento, subtotal, iva_tipo, importe_iva, total_linea,
        created_at, updated_at
    ) VALUES (
        :presupuesto_id, :linea, :producto_id, :descripcion, :cantidad, :unidad_medida, :precio_unitario,
        :descuento_linea, :importe_descuento, :subtotal, :iva_tipo, :importe_iva, :total_linea,
        NOW(), NOW()
    )';

    $stmt = $db->prepare($sql);

    foreach ($lineas as $linea) {
        $stmt->execute([
            ':presupuesto_id' => $presupuestoId,
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
            ':total_linea' => $linea['total_linea'],
        ]);
    }
}

function presupuesto_obtener_por_id(PDO $db, int $id)
{
    $stmt = $db->prepare('SELECT p.*, c.nombre_comercial AS cliente_nombre
        FROM presupuestos p
        INNER JOIN clientes c ON c.id = p.cliente_id
        WHERE p.id = ?');
    $stmt->execute([$id]);
    $presupuesto = $stmt->fetch();
    if (!$presupuesto) {
        return null;
    }

    $stmtLineas = $db->prepare('SELECT * FROM presupuestos_lineas WHERE presupuesto_id = ? ORDER BY linea ASC');
    $stmtLineas->execute([$id]);
    $presupuesto['lineas'] = $stmtLineas->fetchAll();

    return $presupuesto;
}

function presupuesto_validar_cliente(PDO $db, int $clienteId): void
{
    $stmt = $db->prepare('SELECT id FROM clientes WHERE id = ? AND activo = 1');
    $stmt->execute([$clienteId]);
    if (!$stmt->fetchColumn()) {
        throw new InvalidArgumentException('El cliente indicado no existe o está inactivo');
    }
}

function presupuesto_usuario_actual(): int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1;
}
