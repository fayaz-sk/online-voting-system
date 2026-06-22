-- =============================================
-- Online Voting System - Full Database
-- Import this file in phpMyAdmin
-- =============================================

CREATE DATABASE IF NOT EXISTS voting_db;
USE voting_db;

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

INSERT INTO admin (username, password, full_name) VALUES
('admin', MD5('admin123'), 'System Administrator');

-- Voters Table
CREATE TABLE IF NOT EXISTS voters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  voter_id VARCHAR(20) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  has_voted TINYINT(1) DEFAULT 0,
  registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('active','blocked') DEFAULT 'active'
) ENGINE=InnoDB;

-- Sample Voters (password = voter123)
INSERT INTO voters (full_name, email, voter_id, password) VALUES
('Fayaz Ahmed',   'fayaz@gmail.com',  'VOT001', MD5('voter123')),
('Priya Sharma',  'priya@gmail.com',  'VOT002', MD5('voter123')),
('Rahul Verma',   'rahul@gmail.com',  'VOT003', MD5('voter123'));

-- Positions Table
CREATE TABLE IF NOT EXISTS positions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  position_name VARCHAR(100) NOT NULL,
  description VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB;

INSERT INTO positions (position_name, description) VALUES
('President',      'Head of the student body'),
('Vice President', 'Assistant to the President'),
('Secretary',      'Manages records and meetings'),
('Treasurer',      'Manages funds and budget');

-- Candidates Table
CREATE TABLE IF NOT EXISTS candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  position_id INT NOT NULL,
  bio TEXT DEFAULT '',
  photo VARCHAR(255) DEFAULT 'default.png',
  votes INT DEFAULT 0,
  FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO candidates (name, position_id, bio) VALUES
('Arjun Nair',     1, 'Experienced leader with 3 years in student council.'),
('Sneha Reddy',    1, 'Passionate about student welfare and campus development.'),
('Kiran Mehta',    2, 'Dedicated team player with strong communication skills.'),
('Anjali Singh',   2, 'Committed to transparency and student rights.'),
('Rohit Gupta',    3, 'Detail-oriented and excellent in documentation.'),
('Fatima Khan',    3, 'Known for organising events efficiently.'),
('Suresh Babu',    4, 'Finance student with budget management experience.'),
('Divya Pillai',   4, 'Transparent and accountable financial management.');

-- Votes Table
CREATE TABLE IF NOT EXISTS votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voter_id INT NOT NULL,
  candidate_id INT NOT NULL,
  position_id INT NOT NULL,
  voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (voter_id)    REFERENCES voters(id),
  FOREIGN KEY (candidate_id) REFERENCES candidates(id),
  FOREIGN KEY (position_id)  REFERENCES positions(id)
) ENGINE=InnoDB;

-- Election Settings Table
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(50) NOT NULL UNIQUE,
  setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('election_title',  'Student Council Election 2025'),
('election_status', 'open'),
('college_name',    'Your College Name');
