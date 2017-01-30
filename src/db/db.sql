
SET NAMES 'utf8';

DROP TABLE IF EXISTS blog;
CREATE TABLE blog (
  blog_id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  subtitle VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  titleimage MEDIUMINT(9) NOT NULL,
  createdon DATETIME NOT NULL,
  better INT(1) DEFAULT NULL,
  PRIMARY KEY (blog_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 88
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS blog_category;
CREATE TABLE blog_category (
  category_id INT(11) NOT NULL AUTO_INCREMENT,
  category_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (category_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 4
AVG_ROW_LENGTH = 22
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS blog_comments;
CREATE TABLE blog_comments (
  comment_id INT(11) NOT NULL AUTO_INCREMENT,
  blog_id INT(11) NOT NULL DEFAULT 0,
  author VARCHAR(50) NOT NULL DEFAULT '0',
  content TEXT DEFAULT NULL,
  createdon DATETIME DEFAULT NULL,
  moderated INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (comment_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 55
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS images;
CREATE TABLE images (
  image_id INT(11) NOT NULL AUTO_INCREMENT,
  content BLOB NOT NULL,
  mime VARCHAR(16) NOT NULL,
  PRIMARY KEY (image_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 8
AVG_ROW_LENGTH = 29526
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS services;
CREATE TABLE services (
  service_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  servicetype INT(11) NOT NULL,
  details TEXT NOT NULL,
  city VARCHAR(255) NOT NULL,
  city_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (service_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 512
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  userpass VARCHAR(255) NOT NULL,
  createdon DATE NOT NULL,
  email VARCHAR(255) NOT NULL,
  userstate TINYINT(4) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  details TEXT NOT NULL,
  lastlogin DATETIME DEFAULT NULL,
  username VARCHAR(255) NOT NULL,
  userrole INT(11) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE INDEX email (email)
)
ENGINE = MYISAM
AUTO_INCREMENT = 3
AVG_ROW_LENGTH = 352
CHARACTER SET utf8
COLLATE utf8_general_ci;



DROP VIEW IF EXISTS blogview CASCADE;
CREATE OR REPLACE
VIEW blogview
AS
	select `blog`.`blog_id` AS `blog_id`,`blog`.`title` AS `title`,`blog`.`subtitle` AS `subtitle`,`blog`.`content` AS `content`,`blog`.`titleimage` AS `titleimage`,`blog`.`createdon` AS `createdon`,`blog`.`better` AS `better` from `blog`;

DROP VIEW IF EXISTS usersview CASCADE;
CREATE OR REPLACE
VIEW usersview
AS
	select `users`.`user_id` AS `user_id`,`users`.`userpass` AS `userpass`,`users`.`createdon` AS `createdon`,`users`.`userrole` AS `userrole`,`users`.`email` AS `email`,`users`.`userstate` AS `userstate`,`users`.`avatar` AS `avatar`,`users`.`details` AS `details`,`users`.`lastlogin` AS `lastlogin`,`users`.`username` AS `username` from `users`;
