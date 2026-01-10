CREATE DATABASE IF NOT EXISTS med_buddy_db;
USE med_buddy_db;

-- Hospitals Table
CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL
);

-- Doctors Table (Updated with auth)
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    hospital_id INT,
    email VARCHAR(100) UNIQUE, -- Added for login
    password VARCHAR(255),      -- Added for login
    image_path VARCHAR(255) DEFAULT 'default_doctor.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL
);

-- Doctor Availability Table
CREATE TABLE IF NOT EXISTS doctor_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    patient_email VARCHAR(100) NOT NULL,
    patient_phone VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Insert Dummy Data for Hospitals
INSERT INTO hospitals (id, name, location) VALUES
(1, 'City General Hospital', 'Downtown'),
(2, 'Green Valley Clinic', 'Westside'),
(3, 'St. Mary Medical Center', 'North Hills'),
(4, 'Global Health Institute', 'Uptown'),
(5, 'Community Care Center', 'Suburbs')
ON DUPLICATE KEY UPDATE name=name;

-- Insert Dummy Data for Doctors (With Login Credentials: password is 'password123')
-- Note: In a real app, passwords should be hashed. Using plain text for simple demo or MD5 if requested, but I'll use simple plain text for this demo based on context, or simple mapping. 
-- Actually, I will just set a simple password '123456' for everyone for simplicity as this is a prototype.
INSERT INTO doctors (id, name, specialization, department, hospital_id, email, password) VALUES
(1, 'Dr. Jyotirmaya Dash', 'Senior Consultant - Interventional Cardiologist', 'Cardiac Sciences', 1, 'doc1@medbuddy.com', '123456'),
(2, 'Dr. Sarah Smith', 'Consultant - Cardiac Surgeon', 'Cardiac Sciences', 1, 'doc2@medbuddy.com', '123456'),
(3, 'Dr. Alex Doe', 'Senior Consultant - Neurologist', 'Neurology', 2, 'doc3@medbuddy.com', '123456'),
(4, 'Dr. Emily Blunt', 'Specialist - Neurosurgeon', 'Neurology', 2, 'doc4@medbuddy.com', '123456'),
(5, 'Dr. Mark Wood', 'Senior Consultant - Orthopedic Surgeon', 'Orthopedics', 3, 'doc5@medbuddy.com', '123456'),
(6, 'Dr. Lisa Ray', 'Sports Medicine Specialist', 'Orthopedics', 3, 'doc6@medbuddy.com', '123456'),
(7, 'Dr. John Green', 'Senior Consultant - Pediatrician', 'Pediatrics', 4, 'doc7@medbuddy.com', '123456'),
(8, 'Dr. Mary Pop', 'Pediatric Surgeon', 'Pediatrics', 4, 'doc8@medbuddy.com', '123456'),
(9, 'Dr. James King', 'Consultant - General Physician', 'General Medicine', 5, 'doc9@medbuddy.com', '123456'),
(10, 'Dr. Anna Scott', 'Senior Consultant - Internal Medicine', 'General Medicine', 5, 'doc10@medbuddy.com', '123456')
ON DUPLICATE KEY UPDATE email=VALUES(email), password=VALUES(password);

-- Insert Dummy Availability (Everyone available Mon-Fri 9-5)
INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) 
SELECT id, 'Monday', '09:00:00', '17:00:00' FROM doctors;
INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) 
SELECT id, 'Tuesday', '09:00:00', '17:00:00' FROM doctors;
INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) 
SELECT id, 'Wednesday', '09:00:00', '17:00:00' FROM doctors;
INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) 
SELECT id, 'Thursday', '09:00:00', '17:00:00' FROM doctors;
INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) 
SELECT id, 'Friday', '09:00:00', '17:00:00' FROM doctors;

-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
