SET NAMES 'utf8';

CREATE TABLE topics (
  topic_id int NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  content longtext NOT NULL,
  detail text DEFAULT NULL,
  state tinyint DEFAULT 0,
  user_id int DEFAULT NULL,  
  PRIMARY KEY (topic_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

ALTER TABLE topics
ADD INDEX user_id (user_id);

CREATE TABLE nodes (
  node_id int NOT NULL AUTO_INCREMENT,
  pid int NOT NULL,
  title varchar(50) NOT NULL,
  mpath varchar(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  user_id int DEFAULT NULL,
  detail text DEFAULT NULL,
  PRIMARY KEY (node_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

ALTER TABLE nodes
ADD INDEX user_id (user_id);

CREATE TABLE topicnode (
  topic_id int NOT NULL,
  node_id int NOT NULL,
  tn_id int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (tn_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

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
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

ALTER TABLE tags
ADD INDEX topic_id (topic_id);


CREATE TABLE fav (
  fav_id int NOT NULL AUTO_INCREMENT,
  topic_id int NOT NULL,
  user_id int NOT NULL,
  PRIMARY KEY (fav_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

CREATE TABLE users (
  user_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL AUTO_INCREMENT,
  username varchar(64) DEFAULT NULL,
  userpass varchar(255) NOT NULL,
  userlogin varchar(50) NOT NULL,
  disabled tinyint(1) DEFAULT 0,
  PRIMARY KEY (user_id)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;

CREATE TABLE  files (
  file_id int NOT NULL AUTO_INCREMENT,
  topic_id int NOT NULL,
  content longblob NOT NULL,
  details varchar(255) NOT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = MYISAM DEFAULT CHARSET = utf8 ;

ALTER TABLE files
ADD INDEX topic_id (topic_id);


CREATE TABLE keyval (
  keyd varchar(255) NOT NULL,
  vald text NOT NULL,
  PRIMARY KEY (keyd)
)
ENGINE = INNODB DEFAULT CHARSET = utf8 ;



CREATE 
VIEW topicsview
AS
    SELECT
      t.topic_id AS topic_id,
      t.title AS title,
      t.content AS content,
      t.detail AS detail,
      t.state AS state,
      t.user_id AS user_id
    FROM topics t
      

        
CREATE 
VIEW nodesview
AS
    SELECT
      nodes.node_id AS node_id,
      nodes.pid AS pid,
      nodes.title AS title,
      nodes.mpath AS mpath,
      nodes.detail AS detail,
      nodes.user_id AS user_id,
      (SELECT
          COUNT(topicnode.topic_id) AS tcnt
        FROM topicnode
        WHERE (topicnode.node_id = nodes.node_id))  
    FROM nodes;        
   
CREATE 
VIEW topicnodeview
AS
    SELECT
      topicnode.topic_id AS topic_id,
      topicnode.node_id AS node_id,
      topicnode.tn_id AS tn_id,
      topics.title AS title,
      topics.state AS state,
      nodes.ispublic AS ispublic,
      nodes.user_id AS nuser_id,
      topics.user_id AS tuser_id
    FROM topics
      JOIN topicnode
        ON topics.topic_id = topicnode.topic_id
      JOIN nodes
        ON nodes.node_id = topicnode.node_id   
        
INSERT INTO users (  username,   userpass) VALUES(  'admin',   'admin');