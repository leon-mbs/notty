TYPE=VIEW
query=select `cc`.`users`.`user_id` AS `user_id`,`cc`.`users`.`userpass` AS `userpass`,`cc`.`users`.`createdon` AS `createdon`,`cc`.`users`.`userrole` AS `userrole`,`cc`.`users`.`email` AS `email`,`cc`.`users`.`userstate` AS `userstate`,`cc`.`users`.`avatar` AS `avatar`,`cc`.`users`.`details` AS `details`,`cc`.`users`.`lastlogin` AS `lastlogin`,`cc`.`users`.`username` AS `username` from `cc`.`users`
md5=6c11af6f7b22046e3f5756695b91b769
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=2016-12-23 18:55:36
create-version=1
source=select\n    `users`.`user_id` AS `user_id`,\n    `users`.`userpass` AS `userpass`,\n    `users`.`createdon` AS `createdon`,\n    `users`.`userrole` AS `userrole`,\n    `users`.`email` AS `email`,\n    `users`.`userstate` AS `userstate`,\n    `users`.`avatar` AS `avatar`,\n    `users`.`details` AS `details`,\n    `users`.`lastlogin` AS `lastlogin`,\n    `users`.`username` AS `username`\n\n  from\n    `users`
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `cc`.`users`.`user_id` AS `user_id`,`cc`.`users`.`userpass` AS `userpass`,`cc`.`users`.`createdon` AS `createdon`,`cc`.`users`.`userrole` AS `userrole`,`cc`.`users`.`email` AS `email`,`cc`.`users`.`userstate` AS `userstate`,`cc`.`users`.`avatar` AS `avatar`,`cc`.`users`.`details` AS `details`,`cc`.`users`.`lastlogin` AS `lastlogin`,`cc`.`users`.`username` AS `username` from `cc`.`users`
