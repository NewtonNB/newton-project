<?php
// MySQL Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '1234';
$db_name = 'schoolproject';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);

// Initialize tables
function initTables($conn) {
    // Create students table
    $conn->query("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE,
        phone VARCHAR(20),
        usertype VARCHAR(20) DEFAULT 'student',
        password VARCHAR(255),
        status VARCHAR(20) DEFAULT 'Active',
        class_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create teachers table
    $conn->query("CREATE TABLE IF NOT EXISTS teachers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE,
        phone VARCHAR(20),
        subject VARCHAR(255),
        gender VARCHAR(10) NOT NULL,
        joined_on DATE DEFAULT (CURDATE()),
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create classes table
    $conn->query("CREATE TABLE IF NOT EXISTS classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_name VARCHAR(50) NOT NULL UNIQUE,
        level VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create fees table
    $conn->query("CREATE TABLE IF NOT EXISTS fees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        amount_paid DECIMAL(10,2) NOT NULL,
        payment_date DATE DEFAULT (CURDATE()),
        payment_method VARCHAR(20) DEFAULT 'Cash',
        academic_year VARCHAR(9),
        term VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create contact_messages table
    $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        email VARCHAR(150),
        phone VARCHAR(20),
        message TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL DEFAULT NULL
    )");
    // Add deleted_at column if it doesn't exist (for existing tables)
    $conn->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL");
    
    // Create announcements table
    $conn->query("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type VARCHAR(20) DEFAULT 'announcement',
        date DATE,
        time VARCHAR(50),
        location VARCHAR(255),
        speakers TEXT,
        category VARCHAR(50) DEFAULT 'General',
        gallery TEXT,
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create olevel_subjects table
    $conn->query("CREATE TABLE IF NOT EXISTS olevel_subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create alevel_subjects table
    $conn->query("CREATE TABLE IF NOT EXISTS alevel_subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create admission table
    $conn->query("CREATE TABLE IF NOT EXISTS admission (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        dob DATE,
        gender VARCHAR(10),
        address TEXT,
        nationality VARCHAR(100),
        religion VARCHAR(100),
        previous_school VARCHAR(255),
        class_applying VARCHAR(50),
        parent_name VARCHAR(255),
        parent_phone VARCHAR(20),
        email VARCHAR(255),
        phone VARCHAR(20),
        passport_photo VARCHAR(255),
        message TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create events table
    $conn->query("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATE,
        event_time VARCHAR(50),
        location VARCHAR(255),
        max_participants INT DEFAULT 100,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create event_registrations table
    $conn->query("CREATE TABLE IF NOT EXISTS event_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'registered',
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )");
    
    // Create marks table
    $conn->query("CREATE TABLE IF NOT EXISTS marks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        class_id INT NOT NULL,
        subject_name VARCHAR(255) NOT NULL,
        term INT NOT NULL,
        year INT NOT NULL,
        marks DECIMAL(5,2) NOT NULL,
        grade VARCHAR(2),
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_mark (student_id, class_id, subject_name, term, year)
    )");
    
    // Create attendance table
    $conn->query("CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        class_id INT NOT NULL,
        date DATE NOT NULL,
        status ENUM('Present', 'Absent', 'Late', 'Excused') DEFAULT 'Absent',
        marked_by VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_attendance (student_id, class_id, date)
    )");
    
    // Create exam_schedule table
    $conn->query("CREATE TABLE IF NOT EXISTS exam_schedule (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_name VARCHAR(255) NOT NULL,
        class_id INT NOT NULL,
        subject_name VARCHAR(255) NOT NULL,
        exam_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        room VARCHAR(100),
        term INT NOT NULL,
        year INT NOT NULL,
        instructions TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create admin user if it doesn't exist
    $result = $conn->query("SELECT COUNT(*) as count FROM students WHERE usertype = 'admin'");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $stmt = $conn->prepare("INSERT INTO students (username, email, usertype, password, status) VALUES (?, ?, ?, ?, ?)");
        $username = 'admin';
        $email = 'admin@nyabikoni.com';
        $usertype = 'admin';
        $password = 'admin123';
        $status = 'Active';
        $stmt->bind_param('sssss', $username, $email, $usertype, $password, $status);
        $stmt->execute();
        $stmt->close();
    }
    
    // Add sample data if needed
    addSampleData($conn);
}

function addSampleData($conn) {
    // Add sample classes
    $result = $conn->query("SELECT COUNT(*) as count FROM classes");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $classes = [
            ['S1', 'O-Level'], ['S2', 'O-Level'], ['S3', 'O-Level'], 
            ['S4', 'O-Level'], ['S5', 'A-Level'], ['S6', 'A-Level']
        ];
        foreach ($classes as $class) {
            $stmt = $conn->prepare("INSERT INTO classes (class_name, level) VALUES (?, ?)");
            $stmt->bind_param('ss', $class[0], $class[1]);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Add sample students
    $result = $conn->query("SELECT COUNT(*) as count FROM students WHERE usertype = 'student'");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $students = [
            ['John Doe', 'john@student.com', '+256700000101', 'student', 'password123', 'Active'],
            ['Jane Smith', 'jane@student.com', '+256700000102', 'student', 'password123', 'Active'],
            ['Mike Johnson', 'mike@student.com', '+256700000103', 'student', 'password123', 'Active']
        ];
        foreach ($students as $student) {
            $stmt = $conn->prepare("INSERT INTO students (username, email, phone, usertype, password, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $student[0], $student[1], $student[2], $student[3], $student[4], $student[5]);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Add sample teachers
    $result = $conn->query("SELECT COUNT(*) as count FROM teachers");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $teachers = [
            ['Mr. Turyasiima Elly', 'headmaster@nyabikoni.com', '+256775475629', 'Headmaster', 'Male'],
            ['Mrs. Kirabo Patricia', 'kirabo@nyabikoni.com', '+256703599882', 'Deputy Headmistress', 'Female'],
            ['Mr. Nicholas Akampurira', 'nicholas@nyabikoni.com', '+256773078285', 'Mathematics', 'Male']
        ];
        foreach ($teachers as $teacher) {
            $stmt = $conn->prepare("INSERT INTO teachers (full_name, email, phone, subject, gender) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $teacher[0], $teacher[1], $teacher[2], $teacher[3], $teacher[4]);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Add sample O-Level subjects
    $result = $conn->query("SELECT COUNT(*) as count FROM olevel_subjects");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $oLevelSubjects = [
            'ENGLISH', 'MATHEMATICS', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY',
            'HISTORY', 'GEOGRAPHY', 'LITERATURE IN ENGLISH', 'KISWAHILI',
            'CHRISTIAN RELIGIOUS EDUCATION', 'AGRICULTURE', 'COMMERCE'
        ];
        foreach ($oLevelSubjects as $subject) {
            $stmt = $conn->prepare("INSERT INTO olevel_subjects (subject_name) VALUES (?)");
            $stmt->bind_param('s', $subject);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Add sample A-Level subjects
    $result = $conn->query("SELECT COUNT(*) as count FROM alevel_subjects");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $aLevelSubjects = [
            'GENERAL PAPER', 'MATHEMATICS', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY',
            'HISTORY', 'GEOGRAPHY', 'ECONOMICS', 'LITERATURE IN ENGLISH',
            'SUBSIDIARY MATHEMATICS', 'SUBSIDIARY COMPUTER'
        ];
        foreach ($aLevelSubjects as $subject) {
            $stmt = $conn->prepare("INSERT INTO alevel_subjects (subject_name) VALUES (?)");
            $stmt->bind_param('s', $subject);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Add sample events
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $events = [
            ['Annual Sports Day', 'Inter-house sports competition for all students', '2026-02-15', '09:00 AM', 'School Playground', 200],
            ['Science Fair', 'Students showcase their science projects and experiments', '2026-03-10', '10:00 AM', 'Science Laboratory', 150],
            ['Cultural Festival', 'Celebration of diverse cultures with music, dance and food', '2026-04-20', '02:00 PM', 'Main Hall', 300]
        ];
        foreach ($events as $event) {
            $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, location, max_participants) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssi', $event[0], $event[1], $event[2], $event[3], $event[4], $event[5]);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Initialize tables on first load
initTables($conn);
?>