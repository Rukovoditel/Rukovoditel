CREATE TABLE `app_access_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `is_ldap_default` tinyint(1) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO app_access_groups VALUES
('4','Менеджер','1','0','2'),
('5','Разработчик','0','0','1'),
('6','Клиент','0','0','0');

CREATE TABLE `app_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_token` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  `container` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(64) NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `description` text,
  `attachments` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `app_comments_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO app_comments_access VALUES
('4','21','6','view,create'),
('5','21','5','view,create'),
('6','21','4','view,create,update,delete'),
('7','22','5','view,create'),
('8','22','4','view,create,update,delete'),
('9','23','6','view,create'),
('10','23','4','view,create,update,delete'),
('11','24','5','view,create'),
('12','24','4','view,create,update,delete');

CREATE TABLE `app_comments_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_comments_id` (`comments_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `app_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

CREATE TABLE `app_emails_on_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` int(11) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `email_to_name` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `email_from_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `display_in_menu` tinyint(1) DEFAULT '0',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

INSERT INTO app_entities VALUES
('1','0','Пользователи','0','10'),
('21','0','Проекты','0','1'),
('22','21','Задачи','0','1'),
('23','21','Запросы','0','2'),
('24','21','Обсуждения','0','3');

CREATE TABLE `app_entities_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

INSERT INTO app_entities_access VALUES
('28','21','6','view_assigned'),
('29','21','5','view_assigned,reports'),
('30','21','4','view,create,update,delete,reports'),
('31','22','6',''),
('32','22','5','view,create,update,reports'),
('33','22','4','view,create,update,delete,reports'),
('34','23','6','view_assigned,create,update,reports'),
('35','23','5',''),
('36','23','4','view,create,update,delete,reports'),
('37','24','6',''),
('38','24','5','view_assigned,create,update,delete,reports'),
('39','24','4','view,create,update,delete,reports');

CREATE TABLE `app_entities_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

INSERT INTO app_entities_configuration VALUES
('11','1','menu_title','Пользователи'),
('12','1','listing_heading','Пользователи'),
('13','1','window_heading','Информация о пользователе'),
('14','1','insert_button','Добавить пользователя'),
('15','1','use_comments','0'),
('25','21','menu_title',' Проекты'),
('26','21','listing_heading',' Проекты'),
('27','21','window_heading','Информация о проекте'),
('28','21','insert_button','Добавить проект'),
('29','21','email_subject_new_item','Новый проект:'),
('30','21','use_comments','1'),
('31','21','email_subject_new_comment','Новый комментарий к проекту:'),
('32','22','menu_title','Задачи'),
('33','22','listing_heading','Задачи'),
('34','22','window_heading','Информация о задаче'),
('35','22','insert_button','Добавить задачу'),
('36','22','email_subject_new_item','Новая задача'),
('37','22','use_comments','1'),
('38','22','email_subject_new_comment','Новый комментарий к задаче:'),
('39','23','menu_title','Запросы'),
('40','23','listing_heading','Запросы'),
('41','23','window_heading','Информация о запросе'),
('42','23','insert_button','Добавить запрос'),
('43','23','email_subject_new_item','Новый запрос:'),
('44','23','use_comments','1'),
('45','23','email_subject_new_comment','Новый комментарий к запросу'),
('46','24','menu_title','Обсуждения'),
('47','24','listing_heading','Обсуждения'),
('48','24','window_heading','Информация об обсуждении'),
('49','24','insert_button','Добавить обсуждение'),
('50','24','email_subject_new_item','Новое обсуждение:'),
('51','24','use_comments','1'),
('52','24','email_subject_new_comment','Новый комментарий к обсуждению:'),
('53','21','use_editor_in_comments','0'),
('54','22','use_editor_in_comments','0'),
('55','23','use_editor_in_comments','0'),
('56','24','use_editor_in_comments','0');

CREATE TABLE `app_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `entities_list` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `parent_item_id` int(11) NOT NULL DEFAULT '0',
  `linked_id` int(11) NOT NULL DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL,
  `field_5` text,
  `field_6` text,
  `field_7` text,
  `field_8` text,
  `field_9` text,
  `field_10` text,
  `field_12` text,
  `field_13` text,
  `field_14` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_1_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_156` text NOT NULL,
  `field_157` text NOT NULL,
  `field_158` text NOT NULL,
  `field_159` text NOT NULL,
  `field_160` text NOT NULL,
  `field_161` text NOT NULL,
  `field_162` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_21_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_167` text NOT NULL,
  `field_168` text NOT NULL,
  `field_169` text NOT NULL,
  `field_170` text NOT NULL,
  `field_171` text NOT NULL,
  `field_172` text NOT NULL,
  `field_173` text NOT NULL,
  `field_174` text NOT NULL,
  `field_175` text NOT NULL,
  `field_176` text NOT NULL,
  `field_177` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_22_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_182` text NOT NULL,
  `field_183` text NOT NULL,
  `field_184` text NOT NULL,
  `field_185` text NOT NULL,
  `field_186` text NOT NULL,
  `field_194` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_23_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent_item_id` int(11) DEFAULT '0',
  `linked_id` int(11) DEFAULT '0',
  `date_added` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `field_191` text NOT NULL,
  `field_192` text NOT NULL,
  `field_193` text NOT NULL,
  `field_195` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_entity_24_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `short_name` varchar(64) DEFAULT NULL,
  `is_heading` tinyint(1) DEFAULT '0',
  `tooltip` text,
  `tooltip_display_as` varchar(16) NOT NULL DEFAULT '',
  `is_required` tinyint(1) DEFAULT '0',
  `required_message` text,
  `configuration` text,
  `sort_order` int(11) DEFAULT '0',
  `listing_status` tinyint(4) NOT NULL DEFAULT '0',
  `listing_sort_order` int(11) NOT NULL DEFAULT '0',
  `comments_status` tinyint(1) NOT NULL DEFAULT '0',
  `comments_sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_form_tabs_id` (`forms_tabs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8;

INSERT INTO app_fields VALUES
('1','1','1','fieldtype_action','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','0','0','0'),
('2','1','1','fieldtype_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','1','0','0'),
('3','1','1','fieldtype_date_added','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'0','0','0','0'),
('4','1','1','fieldtype_created_by','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'0','0','0','0'),
('5','1','1','fieldtype_user_status','',NULL,NULL,NULL,'',NULL,NULL,NULL,'0','1','7','0','0'),
('6','1','1','fieldtype_user_accessgroups','',NULL,NULL,NULL,'',NULL,NULL,NULL,'1','1','2','0','0'),
('7','1','1','fieldtype_user_firstname','',NULL,NULL,NULL,'',NULL,NULL,'{\"allow_search\":\"1\"}','3','1','4','0','0'),
('8','1','1','fieldtype_user_lastname','',NULL,NULL,NULL,'',NULL,NULL,'{\"allow_search\":\"1\"}','4','1','5','0','0'),
('9','1','1','fieldtype_user_email','',NULL,NULL,NULL,'',NULL,NULL,'{\"allow_search\":\"1\"}','6','1','6','0','0'),
('10','1','1','fieldtype_user_photo','',NULL,NULL,NULL,'',NULL,NULL,NULL,'5','0','0','0','0'),
('12','1','1','fieldtype_user_username','',NULL,'1',NULL,'',NULL,NULL,'{\"allow_search\":\"1\"}','2','1','3','0','0'),
('13','1','1','fieldtype_user_language','',NULL,'0',NULL,'','0',NULL,NULL,'7','0','0','0','0'),
('14','1','1','fieldtype_user_skin','',NULL,'0',NULL,'','0',NULL,NULL,'0','0','0','0','0'),
('152','21','24','fieldtype_action','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','0','0','0'),
('153','21','24','fieldtype_id','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','1','0','0'),
('154','21','24','fieldtype_date_added','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','6','0','0'),
('155','21','24','fieldtype_created_by','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','7','0','0'),
('156','21','24','fieldtype_dropdown','Приоритет',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-medium\"}','0','1','2','1','0'),
('157','21','24','fieldtype_dropdown','Статус',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-medium\"}','1','1','4','1','1'),
('158','21','24','fieldtype_input','Название',NULL,'1',NULL,'','1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','3','0','0'),
('159','21','24','fieldtype_input_date','Дата начала проекта',NULL,'0',NULL,'','0',NULL,NULL,'3','1','5','0','0'),
('160','21','24','fieldtype_textarea_wysiwyg','Описание',NULL,'0',NULL,'','0',NULL,'{\"allow_search\":\"1\"}','4','0','0','0','0'),
('161','21','25','fieldtype_users','Команда',NULL,'0',NULL,'','0',NULL,'{\"display_as\":\"checkboxes\"}','0','0','0','0','0'),
('162','21','24','fieldtype_attachments','Вложения',NULL,'0',NULL,'','0',NULL,NULL,'5','0','0','0','0'),
('163','22','26','fieldtype_action','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','0','0','0'),
('164','22','26','fieldtype_id','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','1','0','0'),
('165','22','26','fieldtype_date_added','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','10','0','0'),
('166','22','26','fieldtype_created_by','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','11','0','0'),
('167','22','26','fieldtype_dropdown','Тип',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-medium\"}','1','1','3','0','0'),
('168','22','26','fieldtype_input','Название',NULL,'1',NULL,'','1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','4','0','0'),
('169','22','26','fieldtype_dropdown','Статус',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-large\"}','3','1','5','1','0'),
('170','22','26','fieldtype_dropdown','Приоритет',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-medium\"}','4','1','2','1','1'),
('171','22','26','fieldtype_users','Назначен на',NULL,'0',NULL,'','0',NULL,'{\"display_as\":\"checkboxes\"}','5','1','6','0','0'),
('172','22','26','fieldtype_textarea_wysiwyg','Описание',NULL,'0',NULL,'','0',NULL,'{\"allow_search\":\"1\"}','6','0','0','0','0'),
('173','22','27','fieldtype_input_numeric','Расчетное время',NULL,'0',NULL,'','0',NULL,'{\"width\":\"input-small\",\"number_format\":\"2/./*\"}','1','1','7','0','0'),
('174','22','27','fieldtype_input_numeric_comments','Затрачено времени',NULL,'0',NULL,'','0',NULL,NULL,'2','1','8','1','2'),
('175','22','27','fieldtype_input_date','Дата начала',NULL,'0',NULL,'','0',NULL,NULL,'3','0','0','0','0'),
('176','22','27','fieldtype_input_date','Дата окончания',NULL,'0',NULL,'','0',NULL,NULL,'4','1','9','0','0'),
('177','22','26','fieldtype_attachments','Вложения',NULL,'0',NULL,'','0',NULL,NULL,'7','0','0','0','0'),
('178','23','28','fieldtype_action','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','0','0','0'),
('179','23','28','fieldtype_id','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','1','0','0'),
('180','23','28','fieldtype_date_added','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','6','0','0'),
('181','23','28','fieldtype_created_by','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','7','0','0'),
('182','23','28','fieldtype_grouped_users','Отдел',NULL,'0',NULL,'','1',NULL,NULL,'0','1','4','1','0'),
('183','23','28','fieldtype_dropdown','Тип',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-large\"}','2','1','2','1','1'),
('184','23','28','fieldtype_input','Тема',NULL,'1',NULL,'','1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','3','1','3','0','0'),
('185','23','28','fieldtype_textarea_wysiwyg','Описание',NULL,'0',NULL,'','0',NULL,'{\"allow_search\":\"1\"}','4','0','0','0','0'),
('186','23','28','fieldtype_dropdown','Статус',NULL,'0',NULL,'','1',NULL,'{\"width\":\"input-large\"}','1','1','5','1','2'),
('187','24','29','fieldtype_action','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','0','0','0'),
('188','24','29','fieldtype_id','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','1','0','0'),
('189','24','29','fieldtype_date_added','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','4','0','0'),
('190','24','29','fieldtype_created_by','',NULL,'0',NULL,'','0',NULL,NULL,'0','1','5','0','0'),
('191','24','29','fieldtype_input','Название',NULL,'1',NULL,'','1',NULL,'{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','1','1','3','0','0'),
('192','24','29','fieldtype_textarea_wysiwyg','Описание',NULL,'0',NULL,'','0',NULL,'{\"allow_search\":\"1\"}','2','0','0','0','0'),
('193','24','29','fieldtype_dropdown','Статус',NULL,'0',NULL,'','0',NULL,'{\"width\":\"input-medium\"}','0','1','2','1','0'),
('194','23','28','fieldtype_attachments','Вложения',NULL,'0',NULL,'','0',NULL,NULL,'5','0','0','0','0'),
('195','24','29','fieldtype_attachments','Вложения',NULL,'0',NULL,'','0',NULL,NULL,'3','0','0','0','0'),
('196','1','1','fieldtype_parent_item_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','100','0','0'),
('197','21','24','fieldtype_parent_item_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','100','0','0'),
('198','22','26','fieldtype_parent_item_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','100','0','0'),
('199','23','28','fieldtype_parent_item_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','100','0','0'),
('200','24','29','fieldtype_parent_item_id','',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'1','100','0','0');

CREATE TABLE `app_fields_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

CREATE TABLE `app_fields_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `fields_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `users` text,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

INSERT INTO app_fields_choices VALUES
('34','0','156','Срочный','0',NULL,'1',NULL),
('35','0','156','Высокий','0',NULL,'2',NULL),
('37','0','157','Новый','0',NULL,'1',NULL),
('38','0','157','Открытый','0',NULL,'2',NULL),
('39','0','157','В ожидании','0',NULL,'3',NULL),
('40','0','157','Закрытый','0',NULL,'4',NULL),
('41','0','157','Отменён','0',NULL,'5',NULL),
('42','0','167','Задача','1',NULL,'1',NULL),
('43','0','167','Изменение','0',NULL,'2',NULL),
('44','0','167','Ошибка','0','#ff7a00','3',NULL),
('45','0','167','Идея','0',NULL,'0',NULL),
('46','0','169','Новый','1',NULL,'0',NULL),
('47','0','169','Открыт','0',NULL,'2',NULL),
('48','0','169','В ожидании','0',NULL,'3',NULL),
('49','0','169','Готов','0',NULL,'4',NULL),
('50','0','169','Завершен','0',NULL,'5',NULL),
('51','0','169','Оплачен','0',NULL,'6',NULL),
('52','0','169','Отменен','0',NULL,'7',NULL),
('53','0','170','Срочный','0','#ff0000','1',NULL),
('54','0','170','Высокий','0',NULL,'2',NULL),
('55','0','170','Средний','1',NULL,'3',NULL),
('56','0','182','Техническая поддержка','0',NULL,'0','19'),
('57','0','183','Запрос на изменение','0',NULL,'1',NULL),
('58','0','183','Сообщить об ошибке','0',NULL,'2',NULL),
('59','0','183','Задать вопрос','0',NULL,'3',NULL),
('60','0','186','Новый','1',NULL,'0',NULL),
('61','0','186','Открытый','0',NULL,'2',NULL),
('62','0','186','Ожидание ответа','0',NULL,'3',NULL),
('63','0','186','Закрыт','0',NULL,'4',NULL),
('64','0','186','Отменен','0',NULL,'5',NULL),
('65','0','193','Открыт','0',NULL,'1',NULL),
('66','0','193','Закрыт','0',NULL,'2',NULL),
('67','0','193','Новый','1',NULL,'0',NULL);

CREATE TABLE `app_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

INSERT INTO app_forms_tabs VALUES
('1','1','Информация','','0'),
('24','21','Информация','','0'),
('25','21','Команда','','1'),
('26','22','Информация','','0'),
('27','22','Время','','1'),
('28','23','Информация','','0'),
('29','24','Информация','','0');

CREATE TABLE `app_global_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_global_lists_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lists_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_lists_id` (`lists_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_items_export_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `templates_fields` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cidx` (`entities_id`,`users_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `reports_type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `menu_icon` varchar(64) NOT NULL DEFAULT '',
  `in_menu` tinyint(1) NOT NULL DEFAULT '0',
  `in_dashboard` tinyint(4) NOT NULL DEFAULT '0',
  `in_dashboard_counter` tinyint(1) NOT NULL DEFAULT '0',
  `in_header` tinyint(1) NOT NULL DEFAULT '0',
  `dashboard_sort_order` int(11) DEFAULT NULL,
  `header_sort_order` int(11) NOT NULL DEFAULT '0',
  `dashboard_counter_sort_order` int(11) NOT NULL DEFAULT '0',
  `listing_order_fields` text NOT NULL,
  `users_groups` text,
  `parent_entity_id` int(11) NOT NULL DEFAULT '0',
  `parent_item_id` int(11) NOT NULL DEFAULT '0',
  `fields_in_listing` text,
  `rows_per_page` int(11) NOT NULL DEFAULT '0',
  `notification_days` varchar(32) NOT NULL,
  `notification_time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_entity_id` (`parent_entity_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

INSERT INTO app_reports VALUES
('59','0','21','0','default','','','0','0','0','0',NULL,'0','0','',NULL,'0','0',NULL,'0','',''),
('61','0','22','0','default','','','0','0','0','0',NULL,'0','0','',NULL,'0','0',NULL,'0','',''),
('63','0','23','0','default','','','0','0','0','0',NULL,'0','0','',NULL,'0','0',NULL,'0','',''),
('66','0','21','19','entity','','','0','0','0','0',NULL,'0','0','',NULL,'0','0',NULL,'0','','');

CREATE TABLE `app_reports_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

INSERT INTO app_reports_filters VALUES
('68','59','157','37,38,39','include','1'),
('70','61','169','46,47,48','include','1'),
('72','63','186','60,61,62','include','1'),
('73','66','157','37,38,39','include','1');

CREATE TABLE `app_reports_filters_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cidx` (`fields_id`,`users_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_sessions` (
  `sesskey` varchar(32) NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`sesskey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_user_filters_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filters_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text,
  `filters_condition` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_filters_id` (`filters_id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_users_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_users_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `fields_in_listing` text NOT NULL,
  `listing_order_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `app_users_search_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_users_reports_id` (`users_id`,`reports_id`),
  KEY `idx_reports_id` (`reports_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_users_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `date_added` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_uei` (`users_id`,`entities_id`) USING BTREE,
  KEY `idx_created_by` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_entities` ADD `notes` TEXT NOT NULL AFTER `name`;
ALTER TABLE `app_fields` ADD `notes` TEXT NOT NULL AFTER `tooltip_display_as`;

CREATE TABLE IF NOT EXISTS `app_comments_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_fields` ADD `comments_forms_tabs_id` INT NOT NULL DEFAULT '0' AFTER `forms_tabs_id`;
ALTER TABLE `app_fields` ADD INDEX `idx_comments_forms_tabs_id` (`comments_forms_tabs_id`);
ALTER TABLE `app_reports` ADD `displays_assigned_only` TINYINT(1) NOT NULL DEFAULT '0' AFTER `users_groups`;

