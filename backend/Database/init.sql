CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
  priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, description, status, priority) VALUES
  ('Sample Task 1', 'This is a sample task', 'pending', 'medium'),
  ('Sample Task 2', 'Another sample task', 'in_progress', 'high'),
  ('Completed Task', 'This task is done', 'completed', 'low');