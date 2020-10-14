DROP DATABASE IF EXISTS api;

CREATE DATABASE api;

use api;

CREATE TABLE IF NOT EXISTS user (
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
    '$2y$10$vlacWXhzkt8uzdL3bwWkfOBsw6.67U4GtH4p2YCi6vEx1gr8HfMNi',
    'Admin',
    'admin@mystixgame.tk'
);

CREATE TABLE IF NOT EXISTS chars (
   id INT NOT NULL AUTO_INCREMENT,
   user_id int NOT NULL,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (user_id) REFERENCES user (id)
);

CREATE TABLE IF NOT EXISTS role (
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id)
);

INSERT INTO role (name) VALUES ('admin'),('user'),('anon');

CREATE TABLE IF NOT EXISTS user_role (
   id INT NOT NULL AUTO_INCREMENT,
   user_id int NOT NULL,
   role_id int NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (user_id) REFERENCES user (id),
   FOREIGN KEY (role_id) REFERENCES role (id)
);

INSERT INTO user_role (user_id,role_id) VALUES (1, 1);

CREATE TABLE IF NOT EXISTS permission (
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(40) NOT NULL,
   PRIMARY KEY (id)
);

INSERT INTO permission (name) VALUES('user_login'), ('user_add'), ('user_get'), ('user_edit'), ('user_delete'), ('user_list'), ('user_getOther'), ('user_editOther'), ('user_deleteOther');
INSERT INTO permission (name) VALUES('role_add'), ('role_get'), ('role_edit'), ('role_delete'), ('role_list');
INSERT INTO permission (name) VALUES('permission_list'), ('permission_addToRole'), ('permission_deleteFromRole');
INSERT INTO permission (name) VALUES('page_add');
INSERT INTO permission (name) VALUES('service_add');
INSERT INTO permission (name) VALUES('character_add'), ('character_get'), ('character_edit'), ('character_delete'), ('character_list');

CREATE TABLE IF NOT EXISTS role_permission (
   id INT NOT NULL AUTO_INCREMENT,
   role_id int NOT NULL,
   permission_id int NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (role_id) REFERENCES role (id),
   FOREIGN KEY (permission_id) REFERENCES permission (id)
);

INSERT INTO role_permission (role_id, permission_id) VALUES (1, 1);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 2);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 3);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 4);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 5);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 6);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 7);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 8);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 9);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 10);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 11);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 12);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 13);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 14);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 15);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 16);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 17);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 18);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 19);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 20);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 21);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 22);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 23);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 24);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 1);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 2);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 3);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 4);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 5);
INSERT INTO role_permission (role_id, permission_id) VALUES (3, 1);
INSERT INTO role_permission (role_id, permission_id) VALUES (3, 2);
