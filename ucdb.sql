-- สร้างฐานข้อมูลถ้ายังไม่มี
CREATE DATABASE IF NOT EXISTS ucdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ใช้ฐานข้อมูล
USE ucdb;

-- ตาราง users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    role ENUM('user', 'moderator', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตาราง categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตาราง threads
CREATE TABLE IF NOT EXISTS threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตาราง comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    thread_id INT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตาราง likes
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (thread_id, user_id),
    FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตาราง reports
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    reported_by INT NOT NULL,
    thread_id INT NULL,
    comment_id INT NULL,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ข้อมูลเริ่มต้นสำหรับ categories
INSERT IGNORE INTO
    categories (name)
VALUES ('General'),
    ('Academics'),
    ('Housing'),
    ('Jobs'),
    ('Events'),
    ('Lost & Found'),
    ('Buy & Sell');

-- ข้อมูลเริ่มต้นสำหรับ users (ใช้ hashed password - แทนที่ด้วยค่าจริงจาก password_hash ใน PHP)
INSERT IGNORE INTO
    users (
        username,
        email,
        password,
        bio,
        role
    )
VALUES (
        'dummy',
        'dummy.uc@email.com',
        '$2y$10$PLACEHOLDER_HASHED_PASSWORD',
        'Just a dummy.',
        'admin'
    );

-- ข้อมูลเริ่มต้นสำหรับ threads (สมมติ category_id และ author_id ตรงกับข้อมูลด้านบน)
INSERT IGNORE INTO
    threads (
        title,
        content,
        category_id,
        author_id
    )
VALUES (
        'Python กับกาแฟยามเช้า',
        'แชร์ประสบการณ์ตอนเขียน Python ตอนตีสามพร้อมกาแฟแก้วที่สามของวัน...',
        2,
        1
    ),
    (
        'AI จะมาแย่งงานจริงไหม?',
        'มาคุยกันแบบตรงๆ ว่า AI แทนคนได้แค่ไหน แล้วเราควรทำตัวยังไงต่อดี',
        1,
        1
    ),
    (
        'สรุปเทคนิคอ่านหนังสือก่อนสอบ 1 คืน',
        'รวมเทคนิคอ่านด่วนก่อนสอบ ทั้งแบบสายหวังรอดและสายยังไม่เปิดหนังสือเลย',
        3,
        1
    ),
    (
        'Linux สำหรับมือใหม่ที่ยังกลัว Terminal',
        'เริ่มต้นยังไงดีไม่ให้หลงทางกับ command line ที่เต็มไปด้วยอักษรขาวดำ',
        2,
        1
    ),
    (
        'รวมเว็บโหลดฟอนต์ฟรีที่ดีจนไม่น่าเชื่อ',
        'เจอขุมทรัพย์ฟอนต์สวยๆ แจกฟรีถูกลิขสิทธิ์มาเลยอยากแบ่งปันกันหน่อย',
        4,
        1
    );

-- ข้อมูลเริ่มต้นสำหรับ comments (สมมติ thread_id ตรงกับ threads ด้านบน)
INSERT IGNORE INTO
    comments (content, thread_id, author_id)
VALUES (
        'น่าสนใจมาก! ขอเคล็ดลับเพิ่มหน่อย',
        1,
        1
    );

-- ข้อมูลเริ่มต้นสำหรับ likes (สมมติ thread_id และ user_id ตรงกัน)
INSERT IGNORE INTO likes (thread_id, user_id) VALUES (1, 1);

-- ข้อมูลเริ่มต้นสำหรับ reports (สมมติ thread_id, comment_id, reported_by ตรงกัน)
INSERT IGNORE INTO
    reports (
        description,
        reported_by,
        thread_id,
        comment_id
    )
VALUES (
        'เนื้อหานี้ดูไม่เหมาะสม',
        1,
        1,
        NULL
    ),
    (
        'คอมเมนต์นี้ไม่สุภาพ',
        1,
        NULL,
        1
    );