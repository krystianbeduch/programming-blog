CREATE TABLE categories (
    category_id   INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (category_id)
);

CREATE TABLE comments (
    comment_id INT NOT NULL AUTO_INCREMENT,
    user_id    INT,
    nickname   VARCHAR(50),
    email      VARCHAR(100),
    content    TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    post_id    INT NOT NULL,
    PRIMARY KEY (comment_id)
--     FOREIGN KEY (post_id) REFERENCES posts(post_id)
--     FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE posts (
    post_id      INT NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255) NOT NULL,
    content      TEXT NOT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_published BOOLEAN,
    user_id      INT NOT NULL,
    category_id  INT NOT NULL,
    PRIMARY KEY (post_id)
--     FOREIGN KEY (user_id) REFERENCES users(user_id),
--     FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE roles (
    role_id   INT NOT NULL AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (role_id)
);

CREATE TABLE users (
    user_id       INT NOT NULL AUTO_INCREMENT,
    nickname      VARCHAR(50) NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id)
--     FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

ALTER TABLE comments ADD FOREIGN KEY (post_id) REFERENCES posts(post_id);
ALTER TABLE comments ADD FOREIGN KEY (user_id) REFERENCES users(user_id);
ALTER TABLE posts ADD FOREIGN KEY (user_id) REFERENCES users(user_id);
ALTER TABLE posts ADD FOREIGN KEY (category_id) REFERENCES categories(category_id);
ALTER TABLE users ADD FOREIGN KEY (role_id) REFERENCES roles(role_id);