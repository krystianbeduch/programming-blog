CREATE TABLE posts (
    post_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    topic VARCHAR(20) NOT NULL,
    nick VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL,
    comment TEXT
);