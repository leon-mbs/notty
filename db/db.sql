
SET NAMES 'utf8';

DROP TABLE IF EXISTS files;
CREATE TABLE files (
  file_id INT(11) NOT NULL AUTO_INCREMENT,
  topic_id INT(11) NOT NULL,
  content BLOB NOT NULL,
  details VARCHAR(255) NOT NULL,
  PRIMARY KEY (file_id),
  INDEX topic_id (topic_id)
)
ENGINE = MYISAM

CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS nodes;
CREATE TABLE nodes (
  node_id INT(11) NOT NULL AUTO_INCREMENT,
  pid INT(11) NOT NULL,
  title VARCHAR(50) NOT NULL,
  mpath VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  user_id INT(11) DEFAULT NULL,
  PRIMARY KEY (node_id),
  INDEX user_id (user_id)
)
ENGINE = MYISAM

CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS tags;
CREATE TABLE tags (
  tag_id INT(11) NOT NULL AUTO_INCREMENT,
  topic_id INT(11) NOT NULL,
  tagvalue VARCHAR(255) NOT NULL,
  PRIMARY KEY (tag_id),
  INDEX topic_id (topic_id)
)
ENGINE = MYISAM

CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS topicnode;
CREATE TABLE topicnode (
  topic_id INT(11) NOT NULL,
  node_id INT(11) NOT NULL,
  tn_id INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (tn_id),
  INDEX node_id (node_id),
  INDEX topic_id (topic_id)
)
ENGINE = MYISAM

CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS topics;
CREATE TABLE topics (
  topic_id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  favorites TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (topic_id)
)
ENGINE = MYISAM


CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  userpass VARCHAR(50) NOT NULL,
  PRIMARY KEY (user_id)
)
ENGINE = MYISAM
CHARACTER SET utf8
COLLATE utf8_general_ci;



DROP VIEW IF EXISTS nodesview CASCADE;
CREATE OR REPLACE

VIEW nodesview
AS
	select `nodes`.`node_id` AS `node_id`,`nodes`.`pid` AS `pid`,`nodes`.`title` AS `title`,`nodes`.`mpath` AS `mpath`,`nodes`.`user_id` AS `user_id`,(select count(`topicnode`.`topic_id`) AS `Count(topic_id)` from `topicnode` where (`topicnode`.`node_id` = `nodes`.`node_id`)) AS `tcnt` from `nodes`;

DROP VIEW IF EXISTS topicnodeview CASCADE;
CREATE OR REPLACE

VIEW topicnodeview
AS
	select `topicnode`.`topic_id` AS `topic_id`,`topicnode`.`node_id` AS `node_id`,`topicnode`.`tn_id` AS `tn_id`,`topics`.`title` AS `title`,`nodes`.`user_id` AS `user_id`,`topics`.`content` AS `content` from ((`topics` join `topicnode` on((`topics`.`topic_id` = `topicnode`.`topic_id`))) join `nodes` on((`nodes`.`node_id` = `topicnode`.`node_id`)));

DROP VIEW IF EXISTS topicsview CASCADE;
CREATE OR REPLACE

VIEW topicsview
AS
	select `t`.`topic_id` AS `topic_id`,`t`.`title` AS `title`,`t`.`content` AS `content`,`t`.`favorites` AS `favorites` from `topics` `t`;
