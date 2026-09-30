CREATE DATABASE IF NOT EXISTS astra_ai;
USE astra_ai;

-- ==========================
-- ADMIN
-- ==========================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO admin(username,password)
VALUES
('admin','admin');


-- ==========================
-- KNOWLEDGE
-- ==========================
CREATE TABLE knowledge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    keywords TEXT NOT NULL
);


-- ==========================
-- UNANSWERED QUESTIONS
-- ==========================
CREATE TABLE unanswered_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    status ENUM('pending','answered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




-- ==========================
-- USER MEMORY
-- ==========================
-- Stores simple user preferences/memories for each anonymous guest.
-- The memory is keyed by a random user ID stored in a secure cookie.
CREATE TABLE IF NOT EXISTS user_memory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) NOT NULL,
    memory_key VARCHAR(100) NOT NULL,
    memory_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_memory (user_id, memory_key)
);


-- ==========================
-- MACHINE LEARNING INTERACTIONS
-- ==========================
-- Stores ML predictions for evaluation and future retraining.
CREATE TABLE IF NOT EXISTS ml_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) NULL,
    question TEXT NOT NULL,
    predicted_intent VARCHAR(100) NOT NULL,
    confidence DECIMAL(6,4) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- DEPARTMENTS
-- ==========================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
);


-- ==========================
-- PERSONNEL
-- ==========================
CREATE TABLE personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    parent_id INT NULL DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (department_id)
    REFERENCES departments(id)
    ON DELETE CASCADE,
    FOREIGN KEY (parent_id)
    REFERENCES personnel(id)
    ON DELETE SET NULL
);

-- ==========================
-- LEARNED QUESTION/ANSWER MEMORY
-- ==========================
-- Stores AI-generated answers so ASTRA can reuse them for future
-- semantically similar questions that are not in the main knowledge table.
CREATE TABLE IF NOT EXISTS learned_qa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    intent VARCHAR(100) DEFAULT 'unknown',
    confidence DECIMAL(6,4) NOT NULL DEFAULT 0,
    use_count INT NOT NULL DEFAULT 0,
    status ENUM('active','pending','disabled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_learned_intent (intent),
    INDEX idx_learned_status (status)
);
