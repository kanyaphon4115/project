-- pet_home.sql
CREATE DATABASE IF NOT EXISTS pet_home;
USE pet_home;

CREATE TABLE dogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(120) NOT NULL,
    age VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO dogs (name, breed, age, gender, description, image)
VALUES
('Buggy', 'Jack Russell', '6 months', 'Male', 'Energetic and playful Jack Russell.', 'pets/dog1.jpg'),
('Peach', 'Shih Tzu', '4 months', 'Female', 'Calm and lovely Shih Tzu puppy.', 'pets/dog2.jpg'),
('Gary', 'Yorkshire Terrier', '3 years', 'Female', 'Small but brave Yorkie.', 'pets/dog3.jpg'),
('Willie', 'Samoyed', '1.5 years', 'Male', 'Fluffy Samoyed with a happy smile.', 'pets/dog4.jpg'),
('Kiwi', 'Yorkshire Terrier', '1 year', 'Male', 'Friendly and curious dog.', 'pets/dog5.jpg');
