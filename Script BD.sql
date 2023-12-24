CREATE DATABASE prueba_tecnica;

USE prueba_tecnica;

CREATE TABLE type_document (
    id_type_document INT NOT NULL PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    UNIQUE KEY uk_type_document_name (name)
);

INSERT INTO type_document VALUES (1, 'C.C'), (2, 'T.I');

CREATE TABLE user (
    id_user INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(150) NOT NULL,
    first_name VARCHAR(150) NOT NULL,
    last_name VARCHAR(150) NOT NULL,
    id_type_document INT NOT NULL,
    document VARCHAR(15) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(200),
    birthdate date,
    is_admin TINYINT NOT NULL,
    UNIQUE KEY uk_user_email (email),
    FOREIGN KEY fk_user_id_type_document (id_type_document) REFERENCES type_document(id_type_document) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO user (
    email,
    password,
    first_name,
    last_name,
    id_type_document,
    document,
    phone,
    address,
    birthdate,
    is_admin
) VALUES (
    'admin@yopmail.com',
    '44d16292b49fc53dee3aab3d5dd420e70c4a7614',
    'Admin',
    'Fénix',
    1,
    '123',
    '123',
    '',
    '',
    1
);

CREATE TABLE z_log_session (
    id_log_session INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    token VARCHAR(40) NOT NULL,
    ip VARCHAR(38) NOT NULL,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    expiration_date TIMESTAMP NOT NULL,
    active TINYINT (1) NOT NULL,
    UNIQUE INDEX uk_log_session_token (token) USING BTREE,
    CONSTRAINT fk_log_session_id_user FOREIGN KEY (id_user) REFERENCES user (id_user) ON DELETE RESTRICT ON UPDATE CASCADE
);