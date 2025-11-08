DELIMITER $$

CREATE PROCEDURE sp_process_order(IN p_order_id INT)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_product_id INT;
    DECLARE v_qty INT;

    DECLARE cur CURSOR FOR
        SELECT product_id, quantity FROM order_items WHERE order_id = p_order_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO v_product_id, v_qty;
        IF done = 1 THEN
            LEAVE read_loop;
        END IF;
        -- Actualiza stock
        UPDATE products SET stock = stock - v_qty WHERE id = v_product_id;
    END LOOP;
    CLOSE cur;

    -- Marca el pedido como procesado
    UPDATE orders SET status = 'processed' WHERE id = p_order_id;

    -- Log
    INSERT INTO logs (type, message, created_at)
    VALUES ('order', CONCAT('Pedido procesado: ', p_order_id), NOW());
END$$

DELIMITER ;