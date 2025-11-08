-- Esquema base para GMIP (MySQL/MariaDB)
CREATE TABLE IF NOT EXISTS providers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  sku VARCHAR(100) NOT NULL UNIQUE,
  price DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  provider_id INT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_products_provider FOREIGN KEY (provider_id) REFERENCES providers(id)
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending'
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL
);

-- Datos de ejemplo
INSERT INTO providers (name, email, phone, created_at) VALUES
('Proveedor Demo', 'proveedor@example.com', '+34 600 000 000', NOW());

INSERT INTO products (name, sku, price, stock, provider_id, created_at) VALUES
('Teclado mecánico', 'SKU-KEY-001', 59.90, 100, 1, NOW()),
('Ratón óptico', 'SKU-MOU-002', 19.90, 150, 1, NOW());