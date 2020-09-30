DROP DATABASE IF EXISTS api;

CREATE DATABASE api;

use api;

CREATE TABLE user (
   id INT NOT NULL AUTO_INCREMENT,
   hash VARCHAR(255) NOT NULL,
   name VARCHAR(40) NOT NULL,
   email VARCHAR(320) NOT NULL,
   PRIMARY KEY (id)
);

INSERT INTO user (
    hash,
    name,
    email
) VALUES (
    'zturtzitziztiutoo',
    'Mystix',
    'mystix@mystixgame.tk'
);


CREATE TABLE chars (
   id INT NOT NULL AUTO_INCREMENT,
   user_id int NOT NULL,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (user_id) REFERENCES user (id)
);


INSERT INTO chars (
    user_id,
    name
) VALUES (
    3,
    'charname1'
);

CREATE TABLE role (
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id)
);


INSERT INTO role (
    name
) VALUES 
    ('admin'),
    ('user')
;

CREATE TABLE user_role (
   id INT NOT NULL AUTO_INCREMENT,
   user_id int NOT NULL,
   role_id int NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (user_id) REFERENCES user (id),
   FOREIGN KEY (role_id) REFERENCES role (id)
);

INSERT INTO user_role (
    user_id,
    role_id
) VALUES (
    3,
    1
);

CREATE TABLE permission (
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id)
);

INSERT INTO permission (
    name
) VALUES 
    ('testberechtigung1'),
    ('testberechtigung2')
;

CREATE TABLE role_permission (
   id INT NOT NULL AUTO_INCREMENT,
   role_id int NOT NULL,
   permission_id int NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (role_id) REFERENCES role (id),
   FOREIGN KEY (permission_id) REFERENCES permission (id)
);

INSERT INTO role_permission (
    role_id,
    permission_id
) VALUES (
    1,
    1
);



















