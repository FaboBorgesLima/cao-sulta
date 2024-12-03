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

DROP TABLE IF EXISTS user_tokens;

CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    token CHAR(44) NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DROP TABLE IF EXISTS vets;

CREATE TABLE vets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DROP TABLE IF EXISTS crmv_registers;

CREATE TABLE crmv_registers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    crmv CHAR(7) NOT NULL,
    state CHAR(2) NOT NULL,
    vet_id INT NOT NULL,
    FOREIGN KEY (vet_id) REFERENCES vets(id)
);

DROP TABLE IF EXISTS permissions;

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    user_id INT NOT NULL,
    vet_id INT NOT NULL,
    FOREIGN KEY (vet_id) REFERENCES vets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

SET foreign_key_checks = 1;