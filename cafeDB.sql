use mydb;
-- user table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255), 
    age INT,
    room_id INT,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- room table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL
);

-- categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    availability BOOLEAN default TRUE,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- order items table

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    room_id INT,
    order_id INT,
    quantity INT NOT NULL DEFAULT 1,
    status ENUM('pending', 'preparing', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
 
 
 -- admins (optional)

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);





