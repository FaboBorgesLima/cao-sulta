SET foreign_key_checks = 0;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    cpf CHAR(11) UNIQUE NOT NULL
);

CREATE TABLE vets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    user_id INT NOT NULL,
    vet_id INT NOT NULL,
    
);

SET foreign_key_checks = 1;