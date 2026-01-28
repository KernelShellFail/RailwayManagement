-- Railway Reservation System Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS railway_system;
USE railway_system;

-- Users table (for both admin and passengers)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('admin', 'passenger') NOT NULL DEFAULT 'passenger',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Trains table
CREATE TABLE trains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    train_number VARCHAR(20) UNIQUE NOT NULL,
    train_name VARCHAR(100) NOT NULL,
    total_seats INT NOT NULL DEFAULT 100,
    available_seats INT NOT NULL DEFAULT 100,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Routes table
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    train_id INT NOT NULL,
    source_station VARCHAR(100) NOT NULL,
    destination_station VARCHAR(100) NOT NULL,
    distance_km DECIMAL(8,2) NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    price_per_seat DECIMAL(8,2) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (train_id) REFERENCES trains(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    route_id INT NOT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    passenger_age INT NOT NULL,
    passenger_gender ENUM('male', 'female', 'other') NOT NULL,
    seats_booked INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    booking_status ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed',
    booking_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, user_type) VALUES 
('admin', 'admin@railway.com', '$2y$10$IGVJserWsfFD/fykQSlwj.O3t6NggDLLAuGqvjMh98Ygbrtlyg.w2', 'System Administrator', 'admin');

-- Insert sample trains
INSERT INTO trains (train_number, train_name, total_seats, available_seats) VALUES 
('EXP001', 'Express One', 120, 120),
('EXP002', 'Express Two', 100, 100),
('REG001', 'Regional One', 80, 80);

-- Insert sample routes
INSERT INTO routes (train_id, source_station, destination_station, distance_km, departure_time, arrival_time, price_per_seat) VALUES 
(1, 'New York', 'Boston', 300.50, '08:00:00', '11:30:00', 45.00),
(1, 'Boston', 'New York', 300.50, '14:00:00', '17:30:00', 45.00),
(2, 'New York', 'Philadelphia', 150.25, '09:00:00', '10:45:00', 35.00),
(2, 'Philadelphia', 'New York', 150.25, '16:00:00', '17:45:00', 35.00),
(3, 'New York', 'Albany', 220.75, '07:30:00', '11:00:00', 40.00);