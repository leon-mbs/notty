CREATE TABLE `nodes` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `mpath` varchar(255) CHARACTER SET latin1 NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`node_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;


CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `tagvalue` varchar(255) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `topicnode` (
  `topic_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `tn_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`tn_id`),
  KEY `topic_id` (`topic_id`),
  KEY `node_id` (`node_id`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

CREATE TABLE `topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `userpass` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

CREATE   VIEW `nodesview` AS
  select
    `nodes`.`node_id` AS `node_id`,
    `nodes`.`pid` AS `pid`,
    `nodes`.`title` AS `title`,
    `nodes`.`mpath` AS `mpath`,
    `nodes`.`user_id` AS `user_id`,
    (
  select
    count(`topicnode`.`topic_id`) AS `Count(topic_id)`
  from
    `topicnode`
  where
    (`topicnode`.`node_id` = `nodes`.`node_id`)) AS `tcnt`
  from
    `nodes`;
    
CREATE   VIEW `topicnodeview` AS
  select
    `topicnode`.`topic_id` AS `topic_id`,
    `topicnode`.`node_id` AS `node_id`,
    `topicnode`.`tn_id` AS `tn_id`,
    `topics`.`title` AS `title`,
    `nodes`.`user_id` AS `user_id`,
    `topics`.`content` AS `content`
  from
    ((`topics` join `topicnode` on((`topics`.`topic_id` = `topicnode`.`topic_id`))) join `nodes` on((`nodes`.`node_id` = `topicnode`.`node_id`)));

CREATE  VIEW `topicsview` AS
  select
    `t`.`topic_id` AS `topic_id`,
    `t`.`title` AS `title`,
    `t`.`content` AS `content`
  from
    `topics` `t`;


