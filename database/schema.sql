-- Database Schema for Product Inventory Management System
-- Used by both Original and Enhanced versions

CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Login attempts table (used by Enhanced version only)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default seed users
-- For Enhanced App (using BCrypt hashes, password is 'admin123' / 'password123')
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@example.com', '$2y$12$n5fTBKPQ6yHfwLbMdXtPluIbpZ41DuZKCOaQ8IOaSFt9dwGdb.Ffm', 'admin'),
('user1', 'user1@example.com', '$2y$12$xanJ8tYQalyumeWewwIcjuJAOaM3/vBGL.xUojmqNF6.Qgbf2p4Ha', 'user');

-- For Original App (using plain text passwords, password is 'admin123' / 'password123')
INSERT INTO users (username, email, password, role) VALUES
('admin_insecure', 'admin_insecure@example.com', 'admin123', 'admin'),
('user1_insecure', 'user1_insecure@example.com', 'password123', 'user');

-- Sample products
INSERT INTO products (name, description, price, quantity, image, user_id) VALUES
('Laptop Dell XPS 15', 'High performance laptop with 16GB RAM', 1299.99, 25, NULL, 1),
('Wireless Mouse', 'Ergonomic wireless mouse with USB receiver', 29.99, 150, NULL, 1),
('USB-C Hub', '7-in-1 USB-C hub with HDMI output', 49.99, 75, NULL, 2),
('Mechanical Keyboard', 'RGB mechanical keyboard with Cherry MX switches', 89.99, 50, NULL, 2);
