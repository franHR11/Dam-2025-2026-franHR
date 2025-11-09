-- =============================================
-- CORRECCIÓN VISTA CLIENTES CON SALDO PENDIENTE
-- =============================================

-- Eliminar vista existente si existe
DROP VIEW IF EXISTS `vista_clientes_saldo_pendiente`;

-- Crear vista corregida sin columna duplicada
CREATE OR REPLACE VIEW `vista_clientes_saldo_pendiente` AS
SELECT
    c.*,
    COALESCE(f.total_pendiente, 0) as saldo_pendiente_facturas
FROM clientes c
LEFT JOIN (
    SELECT
        cliente_id,
        SUM(total - COALESCE(importe_pagado, 0)) as total_pendiente
    FROM facturas
    WHERE estado IN ('pendiente', 'vencida') AND tipo_factura = 'venta'
    GROUP BY cliente_id
) f ON c.id = f.cliente_id
WHERE c.activo = 1 AND COALESCE(f.total_pendiente, 0) > 0;

-- Opcional: Si prefieres renombrar la columna en la vista para que no conflictúe
-- Puedes usar esta versión alternativa:

/*
CREATE OR REPLACE VIEW `vista_clientes_saldo_pendiente` AS
SELECT
    c.id,
    c.codigo,
    c.nombre_comercial,
    c.razon_social,
    c.nif_cif,
    c.direccion,
    c.codigo_postal,
    c.ciudad,
    c.provincia,
    c.pais,
    c.telefono,
    c.telefono2,
    c.email,
    c.web,
    c.tipo_cliente,
    c.forma_pago,
    c.dias_credito,
    c.limite_credito,
    c.importe_acumulado,
    -- Usar un alias diferente para evitar conflicto
    COALESCE(f.total_pendiente, 0) as saldo_facturas_pendientes,
    c.activo,
    c.bloqueado,
    c.observaciones,
    c.contacto_principal,
    c.cargo_contacto,
    c.created_by,
    c.created_at,
    c.updated_at
FROM clientes c
LEFT JOIN (
    SELECT
        cliente_id,
        SUM(total - COALESCE(importe_pagado, 0)) as total_pendiente
    FROM facturas
    WHERE estado IN ('pendiente', 'vencida') AND tipo_factura = 'venta'
    GROUP BY cliente_id
) f ON c.id = f.cliente_id
WHERE c.activo = 1 AND COALESCE(f.total_pendiente, 0) > 0;
*/
