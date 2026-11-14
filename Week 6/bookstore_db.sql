/*
 * Name: Nguyễn Huyền My
 * Student ID: 23070626
 * Assignment: INS3064 - Bookstore Database Design
 * 
 * =========================================================================
 * 3NF NORMALIZATION EXPLANATION
 * =========================================================================
 * This database is designed in Third Normal Form (3NF):
 * 1. 1NF (Atomic Values): No repeating groups or comma-separated lists exist. 
 *    Multiple authors and categories for a single book are broken down into 
 *    proper junction tables (book_authors, book_categories).
 * 2. 2NF (No Partial Dependencies): Every table has a single-column surrogate 
 *    Primary Key (id), meaning all non-key attributes depend on the entire PK.
 *    For junction tables with composite PKs (like order_items), attributes like 
 *    quantity and unit_price depend on the specific combination of order_id and book_id.
 * 3. 3NF (No Transitive Dependencies): Non-key attributes do not depend on other 
 *    non-key attributes. For example, publisher details (name, address) are kept 
 *    in the `publishers` table and referenced via `publisher_id` in `books`, 
 *    rather than storing publisher info directly inside the `books` table.
 *    Note: Storing `unit_price` in `order_items` prevents historical data from 
 *    changing if the current `price` in `books` changes.
 * =========================================================================
 */

CREATE DATABASE IF NOT EXISTS bookstore_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookstore_db;

-- Drop tables if they exist to allow clean re-runs
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS book_categories;
DROP TABLE IF EXISTS book_authors;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS publishers;

-- 1. Table: publishers
CREATE TABLE publishers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address TEXT,
    website VARCHAR(255)
) ENGINE=InnoDB;

-- 2. Table: authors
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    nationality VARCHAR(50)
) ENGINE=InnoDB;

-- 3. Table: categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_category_id INT DEFAULT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Table: books
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    publication_date DATE,
    publisher_id INT NOT NULL,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Table: book_authors (M:N Relationship)
CREATE TABLE book_authors (
    book_id INT NOT NULL,
    author_id INT NOT NULL,
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Table: book_categories (M:N Relationship)
CREATE TABLE book_categories (
    book_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (book_id, category_id),
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 7. Table: customers
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    address TEXT NOT NULL
) ENGINE=InnoDB;

-- 8. Table: orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    total DECIMAL(12, 2) NOT NULL DEFAULT 0.00 CHECK (total >= 0),
    shipping_address TEXT NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 9. Table: order_items (M:N Relationship between Orders and Books)
CREATE TABLE order_items (
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(10, 2) NOT NULL CHECK (unit_price > 0),
    subtotal DECIMAL(12, 2) AS (quantity * unit_price) STORED,
    PRIMARY KEY (order_id, book_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 10. Table: reviews
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    customer_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


-- =========================================================================
-- INSERT SAMPLE DATA
-- =========================================================================

-- Insert 4 Publishers
INSERT INTO publishers (name, address, website) VALUES
('Penguin Books', 'London, UK', 'www.penguin.co.uk'),
('OReilly Media', 'Sebastopol, CA, USA', 'www.oreilly.com'),
('Nha Nam', 'Hanoi, Vietnam', 'www.nhanam.vn'),
('Tre Publishing', 'HCMC, Vietnam', 'www.nxbtre.com.vn');

-- Insert 6 Authors
INSERT INTO authors (name, biography, nationality) VALUES
('J.K. Rowling', 'Author of the Harry Potter series.', 'British'),
('George Orwell', 'Known for dystopian fiction.', 'British'),
('Nguyen Nhat Anh', 'Famous Vietnamese author for youth.', 'Vietnamese'),
('Martin Kleppmann', 'Software engineer and researcher.', 'German'),
('Yuval Noah Harari', 'Historian and philosopher.', 'Israeli'),
('Robert C. Martin', 'Software engineer and author.', 'American');

-- Insert 5 Categories
INSERT INTO categories (name, description, parent_category_id) VALUES
('Fiction', 'Fictional stories and novels', NULL),
('Non-Fiction', 'Factual and informative books', NULL),
('Technology', 'Books about computers and IT', 2),
('Fantasy', 'Magical and mythical stories', 1),
('Vietnamese Literature', 'Books written by Vietnamese authors', 1);

-- Insert 8 Books
INSERT INTO books (title, isbn, price, stock, publication_date, publisher_id) VALUES
('Harry Potter and the Sorcerers Stone', '9780747532743', 25.00, 50, '1997-06-26', 1),
('1984', '9780451524935', 15.50, 100, '1949-06-08', 1),
('Cho Toi Xin Mot Ve Di Tuoi Tho', '9786041042306', 8.00, 200, '2008-01-01', 4),
('Designing Data-Intensive Applications', '9781449373320', 45.00, 30, '2017-03-16', 2),
('Sapiens: A Brief History of Humankind', '9780062316097', 22.00, 80, '2011-01-01', 1),
('Clean Code', '9780132350884', 40.00, 45, '2008-08-01', 2),
('Mat Biec', '9786041042313', 9.50, 150, '1990-01-01', 4),
('Harry Potter and the Chamber of Secrets', '9780747538493', 26.00, 40, '1998-07-02', 1);

-- Insert Book Authors (M:N)
INSERT INTO book_authors (book_id, author_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5), (6, 6), (7, 3), (8, 1);

-- Insert Book Categories (M:N)
INSERT INTO book_categories (book_id, category_id) VALUES
(1, 4), (2, 1), (3, 5), (4, 3), (5, 2), (6, 3), (7, 5), (8, 4);

-- Insert 5 Customers
INSERT INTO customers (name, email, password_hash, address) VALUES
('Nguyen Huyen My', 'my@vnu.edu.vn', 'hash123', 'Hanoi, Vietnam'),
('Le Hai Dang', 'dang@vnu.edu.vn', 'hash456', 'Hanoi, Vietnam'),
('Tran Bao Long', 'long@gmail.com', 'hash789', 'HCMC, Vietnam'),
('John Doe', 'john@example.com', 'hashabc', 'New York, USA'),
('Alice Smith', 'alice@example.com', 'hashdef', 'London, UK');

-- Insert 6 Orders
INSERT INTO orders (customer_id, order_date, status, total, shipping_address) VALUES
(1, '2026-10-15 10:00:00', 'delivered', 85.00, 'Hanoi, Vietnam'),
(2, '2026-10-16 11:30:00', 'shipped', 25.00, 'Hanoi, Vietnam'),
(3, '2026-10-17 14:00:00', 'processing', 67.00, 'HCMC, Vietnam'),
(4, '2026-10-18 09:15:00', 'pending', 40.00, 'New York, USA'),
(5, '2026-10-18 16:45:00', 'delivered', 15.50, 'London, UK'),
(1, '2026-10-19 08:00:00', 'pending', 17.50, 'Hanoi, Vietnam');

-- Insert 12 Order Items (M:N)
INSERT INTO order_items (order_id, book_id, quantity, unit_price) VALUES
(1, 4, 1, 45.00),
(1, 6, 1, 40.00),
(2, 1, 1, 25.00),
(3, 4, 1, 45.00),
(3, 5, 1, 22.00),
(4, 6, 1, 40.00),
(5, 2, 1, 15.50),
(6, 3, 1, 8.00),
(6, 7, 1, 9.50),
(2, 3, 2, 8.00),
(4, 8, 1, 26.00),
(5, 5, 1, 22.00);

-- Insert 10 Reviews
INSERT INTO reviews (book_id, customer_id, rating, comment) VALUES
(1, 1, 5, 'A masterpiece of fantasy!'),
(4, 1, 5, 'Must read for backend developers.'),
(6, 2, 4, 'Very helpful coding principles.'),
(2, 3, 5, 'Classic and thought-provoking.'),
(5, 4, 4, 'Great overview of human history.'),
(3, 1, 5, 'Brings back so many childhood memories.'),
(7, 2, 5, 'Beautiful but sad story.'),
(8, 5, 4, 'Good continuation of the series.'),
(4, 3, 5, 'Complex topics explained well.'),
(1, 4, 5, 'Loved it!');
