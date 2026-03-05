-- create the database
CREATE DATABASE roomora;
USE roomora;

-- customers table
CREATE TABLE customers (
  id int AUTO_INCREMENT PRIMARY KEY,
  full_name varchar(100),
  email varchar(150) UNIQUE,
  password varchar(255) NOT NULL
);

-- rooms table
CREATE TABLE rooms (
  id int AUTO_INCREMENT PRIMARY KEY,
  room_name varchar(50) UNIQUE
);

-- bookings table
CREATE TABLE bookings (
  id int AUTO_INCREMENT PRIMARY KEY,
  customer_id int NOT NULL,
  room_id int NOT NULL,
  start_time datetime NOT NULL,
  end_time datetime NOT NULL,
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- adding the rooms
INSERT INTO rooms (room_name) VALUES
('room A'),
('room B'),
('room C'),
('room D'),
('room E'),
('room F'),
('room G'),
('room H'),
('room I'),
('room J');