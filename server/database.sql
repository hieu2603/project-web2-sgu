-- Table: accounts
CREATE TABLE accounts (
  account_id INT AUTO_INCREMENT PRIMARY KEY,
  account_name VARCHAR(255) NOT NULL,
  account_email VARCHAR(255) NOT NULL UNIQUE,
  account_password VARCHAR(255) NOT NULL,
  account_status VARCHAR(10) NOT NULL DEFAULT 'Active',
  account_role VARCHAR(10) NOT NULL
) ENGINE=InnoDB;

-- Table: categories
CREATE TABLE categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table: products
CREATE TABLE products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(255) NOT NULL,
  category_id INT,
  product_description VARCHAR(1000) NOT NULL,
  product_image VARCHAR(255) NOT NULL,
  product_image2 VARCHAR(255) NOT NULL,
  product_image3 VARCHAR(255) NOT NULL,
  product_image4 VARCHAR(255) NOT NULL,
  product_price DECIMAL(12, 2) NOT NULL,
  product_color VARCHAR(100) NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
) ENGINE=InnoDB;

-- Table: orders
CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  order_cost DECIMAL(12, 2) NOT NULL,
  order_status VARCHAR(255) NOT NULL DEFAULT 'Chưa xác nhận', -- Chưa xác nhận | Chờ thanh toán | Đã xác nhận | Thành công | Hủy đơn
  account_id INT,
  phone_number VARCHAR(12) NOT NULL,
  city VARCHAR(255) NOT NULL,
  district VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (account_id) REFERENCES accounts(account_id)
) ENGINE=InnoDB;

-- Table: order_items
CREATE TABLE order_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  product_quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(order_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
) ENGINE=InnoDB;
