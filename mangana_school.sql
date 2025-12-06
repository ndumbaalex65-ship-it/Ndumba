CREATE DATABASE IF NOT EXISTS mangana_school;
USE mangana_school;

-- Users table for admin and teachers
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    user_type ENUM('admin', 'teacher') DEFAULT 'teacher',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default credentials
INSERT INTO users (username, password, full_name, email, user_type) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'System Administrator', 'admin@mangana.edu.zm', 'admin'),
('teacher1', '$2y$10$YourHashedPasswordHere', 'John Teacher', 'teacher1@mangana.edu.zm', 'teacher');

-- Students table
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    class VARCHAR(20) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female'),
    parent_contact VARCHAR(20),
    parent_email VARCHAR(100),
    address TEXT,
    enrollment_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE subjects (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(10) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default subjects
INSERT INTO subjects (subject_code, subject_name) VALUES
('ICT', 'Information Communication Technology'),
('BIO', 'Biology'),
('ACC', 'Accounts'),
('PHY', 'Physics'),
('CIV', 'Civics'),
('ENG', 'English'),
('LUV', 'Luvale'),
('MAT', 'Mathematics'),
('GEO', 'Geography'),
('CHE', 'Chemistry'),
('HEC', 'Home Economics'),
('PED', 'Physical Education'),
('RE', 'Religious Education');

-- Marks table
CREATE TABLE marks (
    mark_id INT PRIMARY KEY AUTO_INCREMENT,
    student_number VARCHAR(20) NOT NULL,
    subject_code VARCHAR(10) NOT NULL,
    class VARCHAR(20) NOT NULL,
    term ENUM('1', '2', '3') NOT NULL,
    year YEAR NOT NULL,
    marks_obtained DECIMAL(5,2) NOT NULL,
    maximum_marks DECIMAL(5,2) DEFAULT 100,
    grade VARCHAR(2),
    teacher_id INT,
    entered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_number) REFERENCES students(student_number),
    FOREIGN KEY (subject_code) REFERENCES subjects(subject_code),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- Announcements table
CREATE TABLE announcements (
    announcement_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    posted_by INT,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (posted_by) REFERENCES users(id)
);

-- Activity logs
CREATE TABLE activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Messages table
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_type ENUM('student', 'parent', 'teacher', 'all'),
    receiver_id VARCHAR(20),
    subject VARCHAR(200),
    message TEXT,
    message_type ENUM('sms', 'email', 'notification'),
    status ENUM('sent', 'pending', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id)
);
