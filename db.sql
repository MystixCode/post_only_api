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

INSERT INTO role_permission (role_id, permission_id) VALUES (1, 1);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 2);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 3);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 4);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 4);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 3);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 5);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 5);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 6);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 7);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 8);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 9);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 9);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 11);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 12);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 12);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 12);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 13);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 14);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 15);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 15);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 16);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 17);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 19);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 19);
INSERT INTO role_permission (role_id, permission_id) VALUES (3, 19);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 20);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 21);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 22);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 23);
INSERT INTO role_permission (role_id, permission_id) VALUES (2, 23);
INSERT INTO role_permission (role_id, permission_id) VALUES (3, 23);
INSERT INTO role_permission (role_id, permission_id) VALUES (1, 24);

/*TODO insert admin and user role_permission etc*/
