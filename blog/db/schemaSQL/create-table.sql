CREATE TABLE categories (
    category_id   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(1000) NOT NULL,
    PRIMARY KEY (category_id)
);

CREATE TABLE comments (
    comment_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    SMALLINT UNSIGNED,
    username   VARCHAR(50),
    email      VARCHAR(100),
    content    TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    post_id    SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (comment_id)
);

CREATE TABLE posts (
    post_id      SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255) NOT NULL,
    content      TEXT NOT NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id      SMALLINT UNSIGNED NOT NULL,
    category_id  SMALLINT UNSIGNED NOT NULL,
    attachment_id SMALLINT UNSIGNED,
    PRIMARY KEY (post_id)
);

CREATE TABLE posts_attachments (
    attachment_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL, -- Typ MIME (image/jpeg, image/png itp.)
    file_size BIGINT NOT NULL,
    file_data MEDIUMBLOB NOT NULL,
    PRIMARY KEY (attachment_id)
);

CREATE TABLE roles (
    role_id   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (role_id)
);

CREATE TABLE users (
    user_id     SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username    VARCHAR(50) NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    about_me    TEXT,
    is_active   BOOL NOT NULL DEFAULT 0,
    role_id     SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id)
);

ALTER TABLE comments ADD FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE;
ALTER TABLE comments ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL;
ALTER TABLE posts ADD FOREIGN KEY (user_id) REFERENCES users(user_id);
ALTER TABLE posts ADD FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE;
ALTER TABLE posts ADD FOREIGN KEY (attachment_id) REFERENCES posts_attachments(attachment_id) ON DELETE SET NULL;
ALTER TABLE users ADD FOREIGN KEY (role_id) REFERENCES roles(role_id);


CREATE TABLE snake_scores (
    score_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    score SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY(score_id)
);