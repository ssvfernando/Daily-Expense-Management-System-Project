CREATE DATABASE dems_db;
USE dems_db;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'co-admin') DEFAULT 'user',
    status ENUM('active', 'pending', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    cat_id INT PRIMARY KEY AUTO_INCREMENT,
    cat_name VARCHAR(50) NOT NULL,
    user_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    category_id INT,
    item_name VARCHAR(100),
    cost DECIMAL(10, 2),
    expense_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(cat_id) ON DELETE SET NULL
);

INSERT INTO users (username, email, password, role, status) VALUES 
('admin', 'admin@cf.com', '123456', 'admin', 'active'),
('user', 'user@cf.com', '123456', 'user', 'active');

INSERT INTO categories (cat_name, user_id) VALUES 
('Food & Drinks', NULL),
('Transport', NULL),
('Rent', NULL),
('Education', NULL);