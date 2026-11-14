-- setup.sql
DROP DATABASE IF EXISTS contact_manager;
CREATE DATABASE contact_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contact_manager;

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(20),
    group_name VARCHAR(50) DEFAULT 'General',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Chèn 5 dữ liệu mẫu
INSERT INTO contacts (name, email, phone, group_name, notes) VALUES
('Nguyễn Văn A', 'nguyenvana@example.com', '0901234567', 'Work', 'Giám đốc kinh doanh'),
('Trần Thị B', 'tranthib@example.com', '0912345678', 'Family', 'Chị họ'),
('Lê Hoàng C', 'lehoangc@example.com', '0987654321', 'Friends', 'Bạn học đại học'),
('Phạm Quang D', 'phamquangd@example.com', '0971112233', 'General', 'Thợ sửa điện nước'),
('Vũ Bích E', 'vubiche@example.com', '0969998877', 'Work', 'Kế toán công ty');
