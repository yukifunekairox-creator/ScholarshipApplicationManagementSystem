DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS scholarships;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE scholarships (
  scholarship_id INT AUTO_INCREMENT PRIMARY KEY,
  scholarship_name VARCHAR(120) NOT NULL,
  sponsor VARCHAR(120) NOT NULL,
  description TEXT NULL,
  benefit_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  slots INT NOT NULL DEFAULT 1,
  max_gwa DECIMAL(3,2) NOT NULL DEFAULT 3.00,
  deadline DATE NOT NULL,
  status ENUM('Open','Closed') NOT NULL DEFAULT 'Open'
) ENGINE=InnoDB;

CREATE TABLE applications (
  application_id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(12) NOT NULL UNIQUE,
  scholarship_id INT NOT NULL,
  student_id_no VARCHAR(30) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  course VARCHAR(100) NOT NULL,
  year_level VARCHAR(20) NOT NULL,
  gwa DECIMAL(3,2) NOT NULL,
  reason TEXT NOT NULL,
  document VARCHAR(255) NULL,
  status ENUM('Pending','Under Review','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  remarks TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_application_scholarship FOREIGN KEY (scholarship_id) REFERENCES scholarships(scholarship_id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$12$WCB4vEhsRFx7NRSMeOiaFe8SAxhnboCfwTkz.lZFkUWnIJz/lK6Qq', 'Scholarship Officer');

INSERT INTO scholarships (scholarship_name, sponsor, description, benefit_amount, slots, max_gwa, deadline, status) VALUES
('Academic Excellence Grant', 'Office of the Scholarships', 'For students with outstanding academic performance. Covers tuition for one semester.', 15000.00, 5, 1.75, '2026-12-15', 'Open'),
('Dean''s Lister Scholarship', 'College Alumni Association', 'Support for consistent Dean''s Listers who need help with school expenses.', 10000.00, 8, 2.00, '2026-12-31', 'Open'),
('Financial Assistance Program', 'Local Government Unit', 'Assistance for deserving students from low-income families.', 8000.00, 10, 2.50, '2027-01-15', 'Open'),
('Leadership Scholarship', 'Student Affairs Office', 'For active student leaders who show service to the community.', 12000.00, 3, 2.25, '2026-10-30', 'Closed');

INSERT INTO applications (reference_code, scholarship_id, student_id_no, full_name, email, phone, course, year_level, gwa, reason, status, remarks) VALUES
('SCH100001', 1, '2023-00123', 'Maria Santos', 'maria@example.com', '09171234567', 'BS Information Technology', '3rd Year', 1.50, 'I have kept a high grade average since my first year and I want to continue my studies without worrying about tuition.', 'Approved', 'Congratulations! Please visit the scholarship office.'),
('SCH100002', 2, '2022-00456', 'Juan Dela Cruz', 'juan@example.com', '09181234567', 'BS Education', '4th Year', 1.90, 'I am a consistent Dean''s Lister and this grant will help me finish my last year and take the licensure exam.', 'Under Review', NULL),
('SCH100003', 3, '2024-00789', 'Ana Reyes', 'ana@example.com', '09191234567', 'BS Accountancy', '2nd Year', 2.10, 'My parents are farmers and this assistance will help me pay for my books and daily expenses at school.', 'Pending', NULL),
('SCH100004', 1, '2023-00987', 'Pedro Garcia', 'pedro@example.com', '09201234567', 'BS Engineering', '3rd Year', 2.40, 'I would like to apply even though my average is close to the limit because I work hard every semester.', 'Rejected', 'GWA is above the required limit for this scholarship.');
