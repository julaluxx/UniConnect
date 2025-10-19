-- uniconnect_db.sql

CREATE DATABASE IF NOT EXISTS uniconnect_db;

-- ตาราง users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    role ENUM('user', 'moderator', 'admin') DEFAULT 'user',
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตาราง categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตาราง threads
CREATE TABLE IF NOT EXISTS threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ตาราง comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    thread_id INT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ตาราง likes
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(thread_id, user_id),
    FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ตาราง reports
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    reported_by INT NOT NULL,
    thread_id INT,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE
);

-- เพิ่มข้อมูลเริ่มต้นสำหรับ categories
INSERT IGNORE INTO categories (name) VALUES 
('General'), ('Academics'), ('Housing'), ('Jobs'), ('Events'), ('Lost & Found'), ('Buy & Sell');

-- เพิ่มข้อมูลเริ่มติ้นสำหรับ users
INSERT IGNORE INTO users (id, username, email, password, bio, role) VALUES 
(1, 'dummy', 'dummy.uc@email.com', 'dummy-uc', 'Just a dummy.', 'admin');

-- เพิ่มข้อมูลเริ่มติ้นสำหรับ Thread
INSERT IGNORE INTO threads (id, title, content, category_id, author_id, created_at, updated_at)
VALUES
(1, 'Python กับกาแฟยามเช้า',
 'แชร์ประสบการณ์ตอนเขียน Python ตอนตีสามพร้อมกาแฟแก้วที่สามของวัน...',
 2, 1, '2025-10-15 08:32:10', '2025-10-15 08:32:10'),

(2, 'AI จะมาแย่งงานจริงไหม?',
 'มาคุยกันแบบตรงๆ ว่า AI แทนคนได้แค่ไหน แล้วเราควรทำตัวยังไงต่อดี',
 1, 1, '2025-10-16 14:05:44', '2025-10-16 14:05:44'),

(3, 'สรุปเทคนิคอ่านหนังสือก่อนสอบ 1 คืน',
 'รวมเทคนิคอ่านด่วนก่อนสอบ ทั้งแบบสายหวังรอดและสายยังไม่เปิดหนังสือเลย',
 3, 1, '2025-10-17 21:14:03', '2025-10-17 21:14:03'),

(4, 'Linux สำหรับมือใหม่ที่ยังกลัว Terminal',
 'เริ่มต้นยังไงดีไม่ให้หลงทางกับ command line ที่เต็มไปด้วยอักษรขาวดำ',
 2, 1, '2025-10-18 09:12:28', '2025-10-18 09:12:28'),

(5, 'รวมเว็บโหลดฟอนต์ฟรีที่ดีจนไม่น่าเชื่อ',
 'เจอขุมทรัพย์ฟอนต์สวยๆ แจกฟรีถูกลิขสิทธิ์มาเลยอยากแบ่งปันกันหน่อย',
 4, 1, '2025-10-19 11:47:59', '2025-10-19 11:47:59');
