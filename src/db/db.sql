--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 25.03.2016 13:50:45
-- Версия сервера: 5.1.41-community
-- Версия клиента: 4.1
--


--
-- Описание для таблицы erp_account_entry
--
DROP TABLE IF EXISTS erp_account_entry;
CREATE TABLE IF NOT EXISTS erp_account_entry (
  entry_id int(11) NOT NULL AUTO_INCREMENT,
  acc_d int(11) NOT NULL,
  acc_c int(11) NOT NULL,
  amount int(11) NOT NULL,
  document_id int(11) NOT NULL,
  document_date date DEFAULT NULL,
  PRIMARY KEY (entry_id),
  INDEX created (document_date),
  INDEX document_id (document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 125
AVG_ROW_LENGTH = 24
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_account_plan
--
DROP TABLE IF EXISTS erp_account_plan;
CREATE TABLE IF NOT EXISTS erp_account_plan (
  acc_code int(16) NOT NULL,
  acc_name varchar(255) NOT NULL,
  acc_pid int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (acc_code)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 57
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_account_subconto
--
DROP TABLE IF EXISTS erp_account_subconto;
CREATE TABLE IF NOT EXISTS erp_account_subconto (
  subconto_id int(11) NOT NULL AUTO_INCREMENT,
  account_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  document_date date NOT NULL,
  amount int(11) NOT NULL DEFAULT 0,
  quantity int(11) NOT NULL DEFAULT 0,
  customer_id int(11) NOT NULL DEFAULT 0,
  employee_id int(11) NOT NULL DEFAULT 0,
  asset_id int(11) NOT NULL DEFAULT 0,
  extcode int(11) NOT NULL DEFAULT 0,
  stock_id int(11) NOT NULL DEFAULT 0,
  moneyfund_id int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (subconto_id),
  INDEX account_id (account_id),
  INDEX document_date (document_date),
  INDEX document_id (document_id),
  INDEX stock_id (stock_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 175
AVG_ROW_LENGTH = 48
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_bank
--
DROP TABLE IF EXISTS erp_bank;
CREATE TABLE IF NOT EXISTS erp_bank (
  bank_id int(11) NOT NULL AUTO_INCREMENT,
  bank_name varchar(255) NOT NULL,
  detail text NOT NULL,
  PRIMARY KEY (bank_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 70
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_contact
--
DROP TABLE IF EXISTS erp_contact;
CREATE TABLE IF NOT EXISTS erp_contact (
  contact_id int(11) NOT NULL AUTO_INCREMENT,
  firstname varchar(64) NOT NULL,
  middlename varchar(64) DEFAULT NULL,
  lastname varchar(64) NOT NULL,
  email varchar(64) DEFAULT NULL,
  detail text NOT NULL,
  description text DEFAULT NULL,
  customer_id int(11) DEFAULT NULL,
  PRIMARY KEY (contact_id),
  INDEX customer_id (customer_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 35
AVG_ROW_LENGTH = 122
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_customer
--
DROP TABLE IF EXISTS erp_customer;
CREATE TABLE IF NOT EXISTS erp_customer (
  customer_id int(11) NOT NULL AUTO_INCREMENT,
  customer_name varchar(255) DEFAULT NULL,
  detail text NOT NULL,
  contact_id int(11) DEFAULT 0 COMMENT '>0 - физлицо ( ссылка  на  контакт)',
  cust_type int(1) NOT NULL DEFAULT 1 COMMENT '1 - покупатель
2 - продавец
3 - покупатель/продавец
4 - госорганизация
0 - просто стороняя  организация',
  PRIMARY KEY (customer_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 27
AVG_ROW_LENGTH = 289
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_docrel
--
DROP TABLE IF EXISTS erp_docrel;
CREATE TABLE IF NOT EXISTS erp_docrel (
  doc1 int(11) DEFAULT NULL,
  doc2 int(11) DEFAULT NULL,
  INDEX doc1 (doc1),
  INDEX doc2 (doc2)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_document
--
DROP TABLE IF EXISTS erp_document;
CREATE TABLE IF NOT EXISTS erp_document (
  document_id int(11) NOT NULL AUTO_INCREMENT,
  document_number varchar(45) NOT NULL,
  document_date date NOT NULL,
  created datetime NOT NULL,
  updated datetime NOT NULL,
  user_id int(11) NOT NULL,
  content text DEFAULT NULL,
  amount int(11) DEFAULT NULL,
  type_id int(11) NOT NULL,
  state tinyint(4) NOT NULL,
  datatag int(11) DEFAULT NULL,
  PRIMARY KEY (document_id),
  INDEX document_date (document_date)
)
ENGINE = MYISAM
AUTO_INCREMENT = 25
AVG_ROW_LENGTH = 673
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_document_update_log
--
DROP TABLE IF EXISTS erp_document_update_log;
CREATE TABLE IF NOT EXISTS erp_document_update_log (
  document_update_log_id int(11) NOT NULL AUTO_INCREMENT,
  hostname varchar(128) DEFAULT NULL,
  document_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_state tinyint(4) NOT NULL,
  updatedon datetime NOT NULL,
  PRIMARY KEY (document_update_log_id),
  INDEX document_id (document_id),
  INDEX user_id (user_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 117
AVG_ROW_LENGTH = 37
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_files
--
DROP TABLE IF EXISTS erp_files;
CREATE TABLE IF NOT EXISTS erp_files (
  file_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) DEFAULT NULL,
  filename varchar(255) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  item_type int(11) NOT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 12
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_filesdata
--
DROP TABLE IF EXISTS erp_filesdata;
CREATE TABLE IF NOT EXISTS erp_filesdata (
  file_id int(11) DEFAULT NULL,
  filedata longblob DEFAULT NULL,
  UNIQUE INDEX file_id (file_id)
)
ENGINE = MYISAM
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы erp_item
--
DROP TABLE IF EXISTS erp_item;
CREATE TABLE IF NOT EXISTS erp_item (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  itemname varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  measure_id varchar(32) DEFAULT NULL,
  group_id int(11) DEFAULT NULL,
  detail text NOT NULL COMMENT 'цена  для   прайса',
  item_code varchar(16) DEFAULT NULL,
  item_type smallint(6) DEFAULT NULL,
  PRIMARY KEY (item_id),
  UNIQUE INDEX item_code (item_code)
)
ENGINE = MYISAM
AUTO_INCREMENT = 42
AVG_ROW_LENGTH = 268
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_item_group
--
DROP TABLE IF EXISTS erp_item_group;
CREATE TABLE IF NOT EXISTS erp_item_group (
  group_id int(11) NOT NULL AUTO_INCREMENT,
  group_name varchar(255) NOT NULL,
  PRIMARY KEY (group_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 12
AVG_ROW_LENGTH = 28
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_item_measures
--
DROP TABLE IF EXISTS erp_item_measures;
CREATE TABLE IF NOT EXISTS erp_item_measures (
  measure_id int(11) NOT NULL AUTO_INCREMENT,
  measure_name varchar(64) NOT NULL,
  measure_code varchar(10) NOT NULL,
  PRIMARY KEY (measure_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 3
AVG_ROW_LENGTH = 20
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_message
--
DROP TABLE IF EXISTS erp_message;
CREATE TABLE IF NOT EXISTS erp_message (
  message_id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  message text DEFAULT NULL,
  item_id int(11) NOT NULL,
  item_type int(11) DEFAULT NULL,
  PRIMARY KEY (message_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_metadata
--
DROP TABLE IF EXISTS erp_metadata;
CREATE TABLE IF NOT EXISTS erp_metadata (
  meta_id int(11) NOT NULL AUTO_INCREMENT,
  meta_type tinyint(11) NOT NULL,
  description varchar(255) DEFAULT NULL,
  meta_name varchar(255) NOT NULL,
  menugroup varchar(255) DEFAULT NULL,
  notes text NOT NULL,
  disabled tinyint(4) NOT NULL,
  PRIMARY KEY (meta_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 88
AVG_ROW_LENGTH = 107
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_metadata_access
--
DROP TABLE IF EXISTS erp_metadata_access;
CREATE TABLE IF NOT EXISTS erp_metadata_access (
  metadata_access_id int(11) NOT NULL AUTO_INCREMENT,
  metadata_id int(11) NOT NULL,
  role_id int(11) NOT NULL,
  viewacc tinyint(1) NOT NULL DEFAULT 0,
  editacc tinyint(1) NOT NULL DEFAULT 0,
  deleteacc tinyint(1) NOT NULL DEFAULT 0,
  execacc tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (metadata_access_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 43
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_moneyfunds
--
DROP TABLE IF EXISTS erp_moneyfunds;
CREATE TABLE IF NOT EXISTS erp_moneyfunds (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(64) NOT NULL,
  bank int(11) NOT NULL,
  bankaccount varchar(32) NOT NULL,
  ftype smallint(6) NOT NULL COMMENT '0 касса,  1 - основной  счет, 2 -  дополнительный  счет',
  PRIMARY KEY (id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 56
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_department
--
DROP TABLE IF EXISTS erp_staff_department;
CREATE TABLE IF NOT EXISTS erp_staff_department (
  department_id int(11) NOT NULL AUTO_INCREMENT,
  department_name varchar(100) NOT NULL,
  PRIMARY KEY (department_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_employee
--
DROP TABLE IF EXISTS erp_staff_employee;
CREATE TABLE IF NOT EXISTS erp_staff_employee (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  position_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  login varchar(64) DEFAULT NULL,
  contact_id int(11) NOT NULL COMMENT 'физ. лицо',
  detail text DEFAULT NULL,
  hiredate date NOT NULL,
  firedate date DEFAULT NULL,
  PRIMARY KEY (employee_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 23
AVG_ROW_LENGTH = 92
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_staff_position
--
DROP TABLE IF EXISTS erp_staff_position;
CREATE TABLE IF NOT EXISTS erp_staff_position (
  position_id int(11) NOT NULL AUTO_INCREMENT,
  position_name varchar(100) NOT NULL,
  PRIMARY KEY (position_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 34
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_store
--
DROP TABLE IF EXISTS erp_store;
CREATE TABLE IF NOT EXISTS erp_store (
  store_id int(11) NOT NULL AUTO_INCREMENT,
  storename varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  store_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (store_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 18
AVG_ROW_LENGTH = 36
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_store_stock
--
DROP TABLE IF EXISTS erp_store_stock;
CREATE TABLE IF NOT EXISTS erp_store_stock (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  partion int(11) DEFAULT NULL,
  store_id int(11) NOT NULL,
  price int(11) DEFAULT NULL,
  closed tinyint(4) DEFAULT 0 COMMENT ' 1 - неиспользуемая  партия',
  PRIMARY KEY (stock_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 22
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_project
--
DROP TABLE IF EXISTS erp_task_project;
CREATE TABLE IF NOT EXISTS erp_task_project (
  project_id int(11) NOT NULL AUTO_INCREMENT,
  doc_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  projectname varchar(255) NOT NULL,
  PRIMARY KEY (project_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 48
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_task
--
DROP TABLE IF EXISTS erp_task_task;
CREATE TABLE IF NOT EXISTS erp_task_task (
  task_id int(11) NOT NULL AUTO_INCREMENT,
  project_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  hours int(11) DEFAULT NULL,
  status tinyint(4) UNSIGNED NOT NULL,
  taskname varchar(255) DEFAULT NULL,
  createdby int(11) DEFAULT NULL,
  assignedto int(11) DEFAULT NULL,
  priority tinyint(4) UNSIGNED DEFAULT NULL,
  updated datetime DEFAULT NULL,
  PRIMARY KEY (task_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 76
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы erp_task_task_emp
--
DROP TABLE IF EXISTS erp_task_task_emp;
CREATE TABLE IF NOT EXISTS erp_task_task_emp (
  task_emp_id int(11) NOT NULL AUTO_INCREMENT,
  task_id int(11) NOT NULL,
  employee_id int(11) NOT NULL,
  PRIMARY KEY (task_emp_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '  ';

--
-- Описание для таблицы system_options
--
DROP TABLE IF EXISTS system_options;
CREATE TABLE IF NOT EXISTS system_options (
  optname varchar(64) NOT NULL,
  optvalue text NOT NULL,
  UNIQUE INDEX optname (optname)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 258
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы system_roles
--
DROP TABLE IF EXISTS system_roles;
CREATE TABLE IF NOT EXISTS system_roles (
  role_id int(11) NOT NULL AUTO_INCREMENT,
  rolename varchar(64) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY (role_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 40
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы system_session
--
DROP TABLE IF EXISTS system_session;
CREATE TABLE IF NOT EXISTS system_session (
  sesskey varchar(64) NOT NULL DEFAULT '',
  expiry timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expireref varchar(250) DEFAULT '',
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  modified timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  sessdata longtext DEFAULT NULL,
  PRIMARY KEY (sesskey),
  INDEX sess2_expireref (expireref),
  INDEX sess2_expiry (expiry)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 91273
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы system_user_role
--
DROP TABLE IF EXISTS system_user_role;
CREATE TABLE IF NOT EXISTS system_user_role (
  role_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  UNIQUE INDEX role_id (role_id, user_id)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;

--
-- Описание для таблицы system_users
--
DROP TABLE IF EXISTS system_users;
CREATE TABLE IF NOT EXISTS system_users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  userlogin varchar(32) NOT NULL,
  userpass varchar(255) NOT NULL,
  createdon date NOT NULL,
  active int(1) NOT NULL DEFAULT 0,
  email varchar(255) DEFAULT NULL,
  PRIMARY KEY (user_id),
  UNIQUE INDEX userlogin (userlogin)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 32
CHARACTER SET utf8
COLLATE utf8_general_ci;


--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 6.3.358.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 25.03.2016 13:52:13
-- Версия сервера: 5.1.41-community
-- Версия клиента: 4.1
--


--
-- Описание для представления erp_contact_view
--
DROP VIEW IF EXISTS erp_contact_view CASCADE;
CREATE
VIEW erp_contact_view
AS
SELECT
  `erp_contact`.`contact_id` AS `contact_id`,
  `erp_contact`.`firstname` AS `firstname`,
  `erp_contact`.`middlename` AS `middlename`,
  `erp_contact`.`lastname` AS `lastname`,
  CONCAT_WS(' ', `erp_contact`.`lastname`, `erp_contact`.`firstname`, `erp_contact`.`middlename`) AS `fullname`,
  `erp_contact`.`email` AS `email`,
  `erp_contact`.`detail` AS `detail`,
  COALESCE(`e`.`employee_id`, 0) AS `employee`,
  COALESCE(`cc`.`customer_id`, 0) AS `customer`,
  `erp_contact`.`description` AS `description`,
  `cc`.`customer_name` AS `customer_name`
FROM ((`erp_contact`
  LEFT JOIN `erp_staff_employee` `e`
    ON ((`erp_contact`.`contact_id` = `e`.`contact_id`)))
  LEFT JOIN `erp_customer` `cc`
    ON ((`erp_contact`.`customer_id` = `cc`.`customer_id`)));

--
-- Описание для представления erp_customer_view
--
DROP VIEW IF EXISTS erp_customer_view CASCADE;
CREATE
VIEW erp_customer_view
AS
SELECT
  `c`.`customer_id` AS `customer_id`,
  `c`.`customer_name` AS `customer_name`,
  `c`.`detail` AS `detail`,
  0 AS `amount`,
  `c`.`cust_type` AS `cust_type`,
  `c`.`contact_id` AS `contact_id`
FROM `erp_customer` `c`;

--
-- Описание для представления erp_document_view
--
DROP VIEW IF EXISTS erp_document_view CASCADE;
CREATE
VIEW erp_document_view
AS
SELECT
  `d`.`document_id` AS `document_id`,
  `d`.`document_number` AS `document_number`,
  `d`.`document_date` AS `document_date`,
  `d`.`created` AS `created`,
  `d`.`updated` AS `updated`,
  `d`.`user_id` AS `user_id`,
  `d`.`content` AS `content`,
  `d`.`amount` AS `amount`,
  `d`.`type_id` AS `type_id`,
  `u`.`userlogin` AS `userlogin`,
  `d`.`state` AS `state`,
  `d`.`datatag` AS `datatag`,
  `erp_metadata`.`meta_name` AS `meta_name`,
  `erp_metadata`.`description` AS `meta_desc`
FROM ((`erp_document` `d`
  JOIN `system_users` `u`
    ON ((`d`.`user_id` = `u`.`user_id`)))
  JOIN `erp_metadata`
    ON ((`erp_metadata`.`meta_id` = `d`.`type_id`)));

--
-- Описание для представления erp_item_view
--
DROP VIEW IF EXISTS erp_item_view CASCADE;
CREATE
VIEW erp_item_view
AS
SELECT
  `t`.`item_id` AS `item_id`,
  `t`.`detail` AS `detail`,
  `t`.`itemname` AS `itemname`,
  `t`.`description` AS `description`,
  `t`.`measure_id` AS `measure_id`,
  `m`.`measure_name` AS `measure_name`,
  `t`.`group_id` AS `group_id`,
  `g`.`group_name` AS `group_name`,
  `t`.`item_code` AS `item_code`,
  `t`.`item_type` AS `item_type`
FROM ((`erp_item` `t`
  JOIN `erp_item_measures` `m`
    ON ((`t`.`measure_id` = `m`.`measure_id`)))
  LEFT JOIN `erp_item_group` `g`
    ON ((`t`.`group_id` = `g`.`group_id`)));

--
-- Описание для представления erp_message_view
--
DROP VIEW IF EXISTS erp_message_view CASCADE;
CREATE
VIEW erp_message_view
AS
SELECT
  `erp_message`.`message_id` AS `message_id`,
  `erp_message`.`user_id` AS `user_id`,
  `erp_message`.`created` AS `created`,
  `erp_message`.`message` AS `message`,
  `erp_message`.`item_id` AS `item_id`,
  `erp_message`.`item_type` AS `item_type`,
  `system_users`.`userlogin` AS `userlogin`
FROM (`erp_message`
  JOIN `system_users`
    ON ((`erp_message`.`user_id` = `system_users`.`user_id`)));

--
-- Описание для представления erp_metadata_access_view
--
DROP VIEW IF EXISTS erp_metadata_access_view CASCADE;
CREATE
VIEW erp_metadata_access_view
AS
SELECT
  `a`.`viewacc` AS `viewacc`,
  `a`.`editacc` AS `editacc`,
  `a`.`deleteacc` AS `deleteacc`,
  `a`.`execacc` AS `execacc`,
  `r`.`user_id` AS `user_id`,
  `m`.`meta_type` AS `meta_type`,
  `m`.`meta_name` AS `meta_name`
FROM ((`erp_metadata_access` `a`
  JOIN `system_user_role` `r`
    ON ((`a`.`role_id` = `r`.`role_id`)))
  JOIN `erp_metadata` `m`
    ON ((`a`.`metadata_id` = `m`.`meta_id`)));

--
-- Описание для представления erp_staff_employee_view
--
DROP VIEW IF EXISTS erp_staff_employee_view CASCADE;
CREATE
VIEW erp_staff_employee_view
AS
SELECT
  `e`.`employee_id` AS `employee_id`,
  `e`.`position_id` AS `position_id`,
  `e`.`department_id` AS `department_id`,
  `e`.`login` AS `login`,
  `e`.`detail` AS `detail`,
  `c`.`firstname` AS `firstname`,
  `c`.`lastname` AS `lastname`,
  `c`.`middlename` AS `middlename`,
  `d`.`department_name` AS `department_name`,
  `p`.`position_name` AS `position_name`,
  `e`.`contact_id` AS `contact_id`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`, `c`.`middlename`) AS `fullname`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`) AS `shortname`,
  `e`.`firedate` AS `firedate`,
  `e`.`hiredate` AS `hiredate`
FROM (((`erp_staff_employee` `e`
  JOIN `erp_contact` `c`
    ON ((`e`.`contact_id` = `c`.`contact_id`)))
  LEFT JOIN `erp_staff_position` `p`
    ON ((`e`.`position_id` = `p`.`position_id`)))
  LEFT JOIN `erp_staff_department` `d`
    ON ((`e`.`department_id` = `d`.`department_id`)));

--
-- Описание для представления erp_task_project_view
--
DROP VIEW IF EXISTS erp_task_project_view CASCADE;
CREATE
VIEW erp_task_project_view
AS
SELECT
  `erp_task_project`.`project_id` AS `project_id`,
  `erp_task_project`.`doc_id` AS `doc_id`,
  `erp_task_project`.`description` AS `description`,
  `erp_task_project`.`start_date` AS `start_date`,
  `erp_task_project`.`end_date` AS `end_date`,
  `erp_task_project`.`projectname` AS `projectname`,
  1 AS `taskall`,
  0 AS `taskclosed`
FROM `erp_task_project`;

--
-- Описание для представления erp_account_entry_view
--
DROP VIEW IF EXISTS erp_account_entry_view CASCADE;
CREATE
VIEW erp_account_entry_view
AS
SELECT
  `e`.`entry_id` AS `entry_id`,
  `e`.`acc_d` AS `acc_d`,
  `e`.`acc_c` AS `acc_c`,
  `e`.`amount` AS `amount`,
  `e`.`document_id` AS `document_id`,
  `doc`.`document_number` AS `document_number`,
  `doc`.`meta_desc` AS `meta_desc`,
  `doc`.`meta_name` AS `meta_name`,
  `doc`.`document_date` AS `document_date`
FROM (`erp_account_entry` `e`
  JOIN `erp_document_view` `doc`
    ON ((`e`.`document_id` = `doc`.`document_id`)));

--
-- Описание для представления erp_stock_view
--
DROP VIEW IF EXISTS erp_stock_view CASCADE;
CREATE
VIEW erp_stock_view
AS
SELECT
  `erp_store_stock`.`stock_id` AS `stock_id`,
  `erp_store_stock`.`item_id` AS `item_id`,
  `erp_item_view`.`itemname` AS `itemname`,
  `erp_store`.`storename` AS `storename`,
  `erp_store`.`store_id` AS `store_id`,
  `erp_item_view`.`measure_name` AS `measure_name`,
  `erp_store_stock`.`price` AS `price`,
  `erp_store_stock`.`partion` AS `partion`,
  COALESCE(`erp_store_stock`.`closed`, 0) AS `closed`,
  `erp_item_view`.`item_type` AS `item_type`,
  `erp_item_view`.`group_id` AS `group_id`
FROM ((`erp_store_stock`
  JOIN `erp_item_view`
    ON ((`erp_store_stock`.`item_id` = `erp_item_view`.`item_id`)))
  JOIN `erp_store`
    ON ((`erp_store_stock`.`store_id` = `erp_store`.`store_id`)))
WHERE COALESCE((`erp_item_view`.`item_type` <> 3));

--
-- Описание для представления erp_task_task_view
--
DROP VIEW IF EXISTS erp_task_task_view CASCADE;
CREATE
VIEW erp_task_task_view
AS
SELECT
  `t`.`task_id` AS `task_id`,
  `t`.`project_id` AS `project_id`,
  `t`.`description` AS `description`,
  `t`.`start_date` AS `start_date`,
  `t`.`end_date` AS `end_date`,
  `t`.`hours` AS `hours`,
  `t`.`status` AS `status`,
  `t`.`taskname` AS `taskname`,
  `t`.`createdby` AS `createdby`,
  `t`.`assignedto` AS `assignedto`,
  `t`.`priority` AS `priority`,
  `t`.`updated` AS `updated`,
  `u`.`userlogin` AS `creatwedbyname`,
  CONCAT_WS(' ', `a`.`lastname`, `a`.`firstname`) AS `assignedtoname`,
  `p`.`projectname` AS `projectname`
FROM (((`erp_task_task` `t`
  JOIN `erp_task_project` `p`
    ON ((`t`.`project_id` = `p`.`project_id`)))
  JOIN `system_users` `u`
    ON ((`t`.`createdby` = `u`.`user_id`)))
  LEFT JOIN `erp_staff_employee_view` `a`
    ON ((`t`.`assignedto` = `a`.`employee_id`)));

--
-- Описание для представления erp_account_subconto_view
--
DROP VIEW IF EXISTS erp_account_subconto_view CASCADE;
CREATE
VIEW erp_account_subconto_view
AS
SELECT
  `sc`.`subconto_id` AS `subconto_id`,
  `sc`.`account_id` AS `account_id`,
  `sc`.`document_id` AS `document_id`,
  `sc`.`document_date` AS `document_date`,
  CAST((`sc`.`amount` / 100) AS decimal(10, 2)) AS `amount`,
  CAST((`sc`.`quantity` / 1000) AS decimal(10, 2)) AS `quantity`,
  `sc`.`customer_id` AS `customer_id`,
  `sc`.`employee_id` AS `employee_id`,
  `sc`.`asset_id` AS `asset_id`,
  `sc`.`extcode` AS `extcode`,
  `sc`.`stock_id` AS `stock_id`,
  `sc`.`moneyfund_id` AS `moneyfund_id`,
  `dc`.`document_number` AS `document_number`,
  `dc`.`meta_desc` AS `meta_desc`,
  dc.meta_name AS meta_name,
  `cs`.`customer_name` AS `customer_name`,
  (CASE WHEN (`sc`.`employee_id` > 0) THEN `em`.`shortname` ELSE NULL END) AS `employee_name`,
  `mf`.`title` AS `moneyfundname`,
  `it`.`itemname` AS `osname`,
  `st`.`itemname` AS `itemname`,
  CAST((`st`.`partion` / 100) AS decimal(10, 2)) AS `partion`,
  `st`.`storename` AS `storename`,
  `st`.`item_id` AS `item_id`,
  `st`.`store_id` AS `store_id`,
  (CASE WHEN (`sc`.`amount` >= 0) THEN `sc`.`amount` ELSE 0 END) AS `da`,
  (CASE WHEN (`sc`.`amount` < 0) THEN (0 - `sc`.`amount`) ELSE 0 END) AS `ca`,
  (CASE WHEN (`sc`.`quantity` >= 0) THEN `sc`.`quantity` ELSE 0 END) AS `dq`,
  (CASE WHEN (`sc`.`quantity` < 0) THEN (0 - `sc`.`quantity`) ELSE 0 END) AS `cq`
FROM ((((((`erp_account_subconto` `sc`
  JOIN `erp_document_view` `dc`
    ON ((`sc`.`document_id` = `dc`.`document_id`)))
  LEFT JOIN `erp_customer` `cs`
    ON ((`sc`.`customer_id` = `cs`.`customer_id`)))
  LEFT JOIN `erp_staff_employee_view` `em`
    ON ((`sc`.`employee_id` = `em`.`employee_id`)))
  LEFT JOIN `erp_moneyfunds` `mf`
    ON ((`sc`.`moneyfund_id` = `mf`.`id`)))
  LEFT JOIN `erp_item` `it`
    ON ((`sc`.`asset_id` = `it`.`item_id`)))
  LEFT JOIN `erp_stock_view` `st`
    ON ((`sc`.`stock_id` = `st`.`stock_id`)));
	
	