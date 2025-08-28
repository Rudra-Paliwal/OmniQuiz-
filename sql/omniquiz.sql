-- ===== FILE: sql/omniquiz.sql =====
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password_hash VARCHAR(255),
  role VARCHAR(20) DEFAULT 'user'
);

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  text TEXT,
  options_json TEXT,
  correct_option CHAR(1),
  topic_id VARCHAR(100),
  difficulty INT
);

CREATE TABLE quiz_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  start_time DATETIME,
  end_time DATETIME
);

CREATE TABLE responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT,
  question_id INT,
  chosen_option CHAR(1),
  confidence INT,
  time_taken INT,
  hint_cost INT,
  score INT
);
