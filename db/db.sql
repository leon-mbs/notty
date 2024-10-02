SET NAMES 'utf8';

CREATE TABLE topics (
  topic_id int NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  content longtext,
  detail text  ,
  user_id int NOT NULL,
  ispublic tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (topic_id, user_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

ALTER TABLE topics
ADD INDEX user_id (user_id);

CREATE TABLE nodes (
  node_id int NOT NULL AUTO_INCREMENT,
  pid int NOT NULL,
  title varchar(50) NOT NULL,
  mpath varchar(1024) NOT NULL,
  user_id int DEFAULT NULL,
  detail text DEFAULT NULL,
  ispublic tinyint(1) DEFAULT NULL,
  PRIMARY KEY (node_id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE  nodes
ADD INDEX user_id (user_id);

CREATE TABLE topicnode (
  topic_id int NOT NULL,
  node_id int NOT NULL,
  tn_id int NOT NULL AUTO_INCREMENT,
  islink tinyint(1) DEFAULT 0,
  PRIMARY KEY (tn_id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE topicnode
ADD INDEX node_id (node_id);

ALTER TABLE topicnode
ADD INDEX topic_id (topic_id);

CREATE TABLE tags (
  tag_id int NOT NULL AUTO_INCREMENT,
  topic_id int NOT NULL,
  tagvalue varchar(255) NOT NULL,
  PRIMARY KEY (tag_id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE tags
ADD INDEX topic_id (topic_id);


CREATE TABLE fav (
  fav_id int NOT NULL AUTO_INCREMENT,
  topic_id int NOT NULL,
  user_id int NOT NULL,
  PRIMARY KEY (fav_id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

CREATE TABLE users (
  user_id int NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  userpass varchar(255) NOT NULL,
  userlogin varchar(64) DEFAULT NULL,
  disabled tinyint(1) DEFAULT 0,
  PRIMARY KEY (user_id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

CREATE TABLE files (
  file_id int NOT NULL AUTO_INCREMENT,
  topic_id int NOT NULL,
  content longblob NOT NULL,
  details varchar(255) NOT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = MYISAM,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE files
ADD INDEX topic_id (topic_id);


CREATE TABLE keyval (
  keyd varchar(255) NOT NULL,
  vald text NOT NULL,
  PRIMARY KEY (keyd)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;



CREATE VIEW topicsview
AS
SELECT
  t.topic_id AS topic_id,
  t.title AS title,
  t.content AS content,
  t.detail AS detail,
  t.acctype AS acctype,
  t.user_id AS user_id,
  (CASE WHEN (COALESCE(u.username, '') = '') THEN u.userlogin ELSE u.username END) AS username
FROM (topics t
  JOIN users u
    ON ((t.user_id = u.user_id)));

        
CREATE VIEW nodesview
AS
SELECT
  nodes.node_id AS node_id,
  nodes.pid AS pid,
  nodes.title AS title,
  nodes.mpath AS mpath,
  nodes.user_id AS user_id,
  nodes.detail AS detail,
  nodes.ispublic AS ispublic,
  (SELECT
      COUNT(topicnode.topic_id) AS tcnt
    FROM topicnode
    WHERE (topicnode.node_id = nodes.node_id)) AS Name_exp_6
FROM nodes;
          
   
CREATE VIEW topicnodeview
AS
SELECT
  topicnode.topic_id AS topic_id,
  topicnode.node_id AS node_id,
  topicnode.tn_id AS tn_id,
  topicnode.islink AS islink,
  topics.title AS title,
  topics.acctype AS acctype,
  topics.content AS content,
  nodes.ispublic AS ispublic,
  nodes.user_id AS nuser_id,
  topics.user_id AS tuser_id
FROM ((topics
  JOIN topicnode
    ON ((topics.topic_id = topicnode.topic_id)))
  JOIN nodes
    ON ((nodes.node_id = topicnode.node_id)));


        
INSERT INTO users (  userlogin,   userpass) VALUES(  'admin',   'admin');