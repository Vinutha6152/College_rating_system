CREATE DATABASE college_rating_system;

USE college_rating_system;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Colleges Table
CREATE TABLE colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Ratings Table
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    college_id INT NOT NULL,
    placement_rating INT NOT NULL,
    academics_rating INT NOT NULL,
    sports_rating INT NOT NULL,
    cafeteria_rating INT NOT NULL,
    dance_club_rating INT NOT NULL,
    overall_rating FLOAT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (college_id) REFERENCES colleges(id)
);

-- Add some initial data to the colleges table
INSERT INTO colleges (name) VALUES 
('College A'),
('College B'),
('College C');
