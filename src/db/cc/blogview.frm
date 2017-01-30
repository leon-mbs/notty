TYPE=VIEW
query=select `disqit`.`blog`.`blog_id` AS `blog_id`,`disqit`.`blog`.`title` AS `title`,`disqit`.`blog`.`subtitle` AS `subtitle`,`disqit`.`blog`.`content` AS `content`,`disqit`.`blog`.`titleimage` AS `titleimage`,`disqit`.`blog`.`category_id` AS `category_id`,`disqit`.`blog`.`createdon` AS `createdon`,`disqit`.`blog`.`better` AS `better`,`disqit`.`blog_category`.`category_name` AS `category_name` from (`disqit`.`blog` join `disqit`.`blog_category` on((`disqit`.`blog`.`category_id` = `disqit`.`blog_category`.`category_id`)))
md5=c2121cb3c34775cfe1f972c3283d9d8a
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=1
with_check_option=0
timestamp=2015-12-18 18:49:45
create-version=1
source=select \n    `blog`.`blog_id` AS `blog_id`,\n    `blog`.`title` AS `title`,\n    `blog`.`subtitle` AS `subtitle`,\n    `blog`.`content` AS `content`,\n    `blog`.`titleimage` AS `titleimage`,\n    `blog`.`category_id` AS `category_id`,\n    `blog`.`createdon` AS `createdon`,\n    `blog`.`better` AS `better`,\n    `blog_category`.`category_name` AS `category_name` \n  from \n    (`blog` join `blog_category` on((`blog`.`category_id` = `blog_category`.`category_id`)))
client_cs_name=latin1
connection_cl_name=latin1_swedish_ci
view_body_utf8=select `disqit`.`blog`.`blog_id` AS `blog_id`,`disqit`.`blog`.`title` AS `title`,`disqit`.`blog`.`subtitle` AS `subtitle`,`disqit`.`blog`.`content` AS `content`,`disqit`.`blog`.`titleimage` AS `titleimage`,`disqit`.`blog`.`category_id` AS `category_id`,`disqit`.`blog`.`createdon` AS `createdon`,`disqit`.`blog`.`better` AS `better`,`disqit`.`blog_category`.`category_name` AS `category_name` from (`disqit`.`blog` join `disqit`.`blog_category` on((`disqit`.`blog`.`category_id` = `disqit`.`blog_category`.`category_id`)))
