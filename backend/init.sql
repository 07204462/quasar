CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  completed BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, description, completed) VALUES
  ('Sample Task 1', 'This is a sample task', FALSE),
  ('Sample Task 2', 'Another sample task', FALSE),
  ('Completed Task', 'This task is done', TRUE);
