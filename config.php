<?php
// Simple database configuration using SQLite with mysqli-like interface
$db_file = __DIR__ . '/schoolproject.db';

// Create a simple wrapper class to make PDO work like mysqli
class SimpleDB {
    private $pdo;
    public $error = '';
    public $connect_error = '';
    
    public function __construct($db_file) {
        $this->pdo = new PDO("sqlite:$db_file");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initTables();
    }
    
    public function query($sql) {
        try {
            $stmt = $this->pdo->query($sql);
            return new SimpleResult($stmt);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
    
    public function prepare($sql) {
        return new SimpleStatement($this->pdo->prepare($sql), $this->pdo);
    }
    
    public function real_escape_string($string) {
        return str_replace("'", "''", $string);
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    private function initTables() {
        // Create students table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            phone VARCHAR(20),
            usertype VARCHAR(20) DEFAULT 'student',
            password VARCHAR(255),
            status VARCHAR(20) DEFAULT 'Active',
            class_id INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create teachers table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS teachers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            phone VARCHAR(20),
            subject VARCHAR(255),
            gender VARCHAR(10) NOT NULL,
            joined_on DATE DEFAULT (date('now')),
            status VARCHAR(20) DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create classes table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS classes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            class_name VARCHAR(50) NOT NULL UNIQUE,
            level VARCHAR(20) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create fees table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS fees (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER,
            amount_paid DECIMAL(10,2) NOT NULL,
            payment_date DATE DEFAULT (date('now')),
            payment_method VARCHAR(20) DEFAULT 'Cash',
            academic_year VARCHAR(9),
            term VARCHAR(20) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create contact_messages table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email VARCHAR(150),
            phone VARCHAR(20),
            message TEXT,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create announcements table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            type VARCHAR(20) DEFAULT 'announcement',
            date DATE,
            time VARCHAR(50),
            location VARCHAR(255),
            speakers TEXT,
            category VARCHAR(50) DEFAULT 'General',
            gallery TEXT,
            content TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Add category column if it doesn't exist (for existing databases)
        try {
            $columns = $this->pdo->query("PRAGMA table_info(announcements)")->fetchAll();
            $hasCategory = false;
            foreach ($columns as $column) {
                if ($column['name'] === 'category') {
                    $hasCategory = true;
                    break;
                }
            }
            if (!$hasCategory) {
                $this->pdo->exec("ALTER TABLE announcements ADD COLUMN category VARCHAR(50) DEFAULT 'General'");
            }
        } catch (Exception $e) {
            // Column already exists or other error, ignore
        }
        
        // Create olevel_subjects table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS olevel_subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject_name VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create alevel_subjects table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS alevel_subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject_name VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create admission table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS admission (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(20),
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create events table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date DATE,
            event_time VARCHAR(50),
            location VARCHAR(255),
            max_participants INTEGER DEFAULT 100,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create event_registrations table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS event_registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_id INTEGER,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(20),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'registered'
        )");
        
        // Create admin user if it doesn't exist
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE usertype = 'admin'");
        $stmt->execute();
        $adminCount = $stmt->fetchColumn();
        
        if ($adminCount == 0) {
            $stmt = $this->pdo->prepare("INSERT INTO students (username, email, usertype, password, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['admin', 'admin@nyabikoni.com', 'admin', 'admin123', 'Active']);
        }
        
        // Add sample data if needed
        $this->addSampleData();
    }
    
    private function addSampleData() {
        // Add sample classes
        $classCount = $this->pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
        if ($classCount == 0) {
            $classes = [
                ['S1', 'O-Level'], ['S2', 'O-Level'], ['S3', 'O-Level'], 
                ['S4', 'O-Level'], ['S5', 'A-Level'], ['S6', 'A-Level']
            ];
            foreach ($classes as $class) {
                $stmt = $this->pdo->prepare("INSERT INTO classes (class_name, level) VALUES (?, ?)");
                $stmt->execute($class);
            }
        }
        
        // Add sample students
        $studentCount = $this->pdo->query("SELECT COUNT(*) FROM students WHERE usertype = 'student'")->fetchColumn();
        if ($studentCount == 0) {
            $students = [
                ['John Doe', 'john@student.com', '+256700000101', 'student', 'password123', 'Active'],
                ['Jane Smith', 'jane@student.com', '+256700000102', 'student', 'password123', 'Active'],
                ['Mike Johnson', 'mike@student.com', '+256700000103', 'student', 'password123', 'Active']
            ];
            foreach ($students as $student) {
                $stmt = $this->pdo->prepare("INSERT INTO students (username, email, phone, usertype, password, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute($student);
            }
        }
        
        // Add sample teachers
        $teacherCount = $this->pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
        if ($teacherCount == 0) {
            $teachers = [
                ['Mr. Turyasiima Elly', 'headmaster@nyabikoni.com', '+256775475629', 'Headmaster', 'Male'],
                ['Mrs. Kirabo Patricia', 'kirabo@nyabikoni.com', '+256703599882', 'Deputy Headmistress', 'Female'],
                ['Mr. Nicholas Akampurira', 'nicholas@nyabikoni.com', '+256773078285', 'Mathematics', 'Male']
            ];
            foreach ($teachers as $teacher) {
                $stmt = $this->pdo->prepare("INSERT INTO teachers (full_name, email, phone, subject, gender) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute($teacher);
            }
        }
        
        // Add sample O-Level subjects
        $oLevelCount = $this->pdo->query("SELECT COUNT(*) FROM olevel_subjects")->fetchColumn();
        if ($oLevelCount == 0) {
            $oLevelSubjects = [
                'ENGLISH', 'MATHEMATICS', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY',
                'HISTORY', 'GEOGRAPHY', 'LITERATURE IN ENGLISH', 'KISWAHILI',
                'CHRISTIAN RELIGIOUS EDUCATION', 'AGRICULTURE', 'COMMERCE'
            ];
            foreach ($oLevelSubjects as $subject) {
                $stmt = $this->pdo->prepare("INSERT INTO olevel_subjects (subject_name) VALUES (?)");
                $stmt->execute([$subject]);
            }
        }
        
        // Add sample A-Level subjects
        $aLevelCount = $this->pdo->query("SELECT COUNT(*) FROM alevel_subjects")->fetchColumn();
        if ($aLevelCount == 0) {
            $aLevelSubjects = [
                'GENERAL PAPER', 'MATHEMATICS', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY',
                'HISTORY', 'GEOGRAPHY', 'ECONOMICS', 'LITERATURE IN ENGLISH',
                'SUBSIDIARY MATHEMATICS', 'SUBSIDIARY COMPUTER'
            ];
            foreach ($aLevelSubjects as $subject) {
                $stmt = $this->pdo->prepare("INSERT INTO alevel_subjects (subject_name) VALUES (?)");
                $stmt->execute([$subject]);
            }
        }
        
        // Add sample events
        $eventCount = $this->pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
        if ($eventCount == 0) {
            $events = [
                ['Annual Sports Day', 'Inter-house sports competition for all students', '2026-02-15', '09:00 AM', 'School Playground', 200],
                ['Science Fair', 'Students showcase their science projects and experiments', '2026-03-10', '10:00 AM', 'Science Laboratory', 150],
                ['Cultural Festival', 'Celebration of diverse cultures with music, dance and food', '2026-04-20', '02:00 PM', 'Main Hall', 300]
            ];
            foreach ($events as $event) {
                $stmt = $this->pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, max_participants) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute($event);
            }
        }
    }
}

class SimpleResult {
    private $stmt;
    private $data = [];
    private $index = 0;
    public $num_rows = 0;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
        if ($stmt) {
            $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->num_rows = count($this->data);
        }
    }
    
    public function fetch_assoc() {
        if ($this->index < count($this->data)) {
            return $this->data[$this->index++];
        }
        return null;
    }
    
    public function fetch_row() {
        if ($this->index < count($this->data)) {
            return array_values($this->data[$this->index++]);
        }
        return null;
    }
}

class SimpleStatement {
    private $stmt;
    private $pdo;
    public $error = '';
    
    public function __construct($stmt, $pdo = null) {
        $this->stmt = $stmt;
        $this->pdo = $pdo;
    }
    
    public function bind_param($types, ...$params) {
        // Simple parameter binding - just store for execute
        $this->params = $params;
        return true;
    }
    
    public function execute() {
        try {
            if (isset($this->params)) {
                return $this->stmt->execute($this->params);
            } else {
                return $this->stmt->execute();
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
    
    public function get_result() {
        return new SimpleResult($this->stmt);
    }
    
    public function close() {
        // PDO doesn't need explicit closing
        return true;
    }
}

try {
    $conn = new SimpleDB($db_file);
} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>