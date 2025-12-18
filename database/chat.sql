CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES
(1, 2, 'Hello, can I ask you something?'),
(2, 1, 'Sure, how can I help you?'),
(1, 2, 'Where can I pick up the dog?'),
(2, 1, 'You can come to Chonburi 😊');
