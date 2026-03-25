-- 1. Setup the Database
CREATE DATABASE IF NOT EXISTS roomora;
USE roomora;

-- 2. Clean Slate: Remove old tables if they exist
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS customers;
SET FOREIGN_KEY_CHECKS = 1;

-- 3. Create Customers Table
CREATE TABLE customers (
  id int AUTO_INCREMENT PRIMARY KEY,
  full_name varchar(100),
  email varchar(150) UNIQUE,
  password varchar(255) NOT NULL
);

-- 4. Create Expanded Rooms Table
CREATE TABLE rooms (
  id int AUTO_INCREMENT PRIMARY KEY,
  room_name varchar(50) UNIQUE,
  description TEXT,
  price_per_hour DECIMAL(10, 2) DEFAULT 50.00,
  capacity INT DEFAULT 2,
  amenities VARCHAR(255) DEFAULT 'Wi-Fi, Smart TV, Coffee Maker'
);

-- 5. Create Bookings Table
CREATE TABLE bookings (
  id int AUTO_INCREMENT PRIMARY KEY,
  customer_id int NOT NULL,
  room_id int NOT NULL,
  start_time datetime NOT NULL,
  end_time datetime NOT NULL,
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- 6. Insert Detailed Luxury Data
INSERT INTO rooms (room_name, description, price_per_hour, capacity, amenities) VALUES
('room A', 'An elegant space designed for focus and luxury. Features floor-to-ceiling windows with a city view.', 75.00, 2, 'High-Speed Wi-Fi, 4K Projector, Mini Bar, AC'),
('room B', 'Our largest suite, perfect for team collaborations or executive retreats.', 120.00, 6, 'Conference Table, Wi-Fi, Catering, Sound System'),
('room C', 'A cozy, quiet room ideal for deep work and overnight stays.', 45.00, 1, 'Wi-Fi, Queen Bed, Coffee Maker'),
('room D', 'Standard executive room with modern decor and premium ergonomic furniture.', 60.00, 2, 'Wi-Fi, Smart TV, AC'),
('room E', 'Sophisticated minimalist design with a dedicated workspace and high-end finishes.', 65.00, 2, 'Ergonomic Desk, Wi-Fi, Coffee Station'),
('room F', 'Bright and airy suite featuring natural light and state-of-the-art climate control.', 70.00, 2, 'Wi-Fi, Smart TV, AC, Terrace Access'),
('room G', 'A premium corner suite offering privacy and an expanded lounge area.', 85.00, 3, 'Lounge Seating, Wi-Fi, Mini Bar, AC'),
('room H', 'Modern industrial style suite with premium acoustics for focused meetings.', 90.00, 4, 'Soundproofing, 55" Display, Wi-Fi, AC'),
('room I', 'Classic luxury suite with plush furnishings and a private refreshments bar.', 110.00, 2, 'Premium Bedding, Mini Bar, Wi-Fi, AC'),
('room J', 'The Penthouse Suite: The ultimate Roomora experience with panoramic views.', 250.00, 4, 'Full Bar, 75" TV, Balcony, Dedicated Wi-Fi');