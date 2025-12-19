CREATE TABLE adopt_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dog_id INT NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    contact VARCHAR(255) NOT NULL,
    area TEXT,
    experience VARCHAR(255),
    time_home INT,
    reason TEXT,
    family_agree VARCHAR(20),
    care_time VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
