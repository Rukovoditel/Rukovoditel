ALTER TABLE `app_reports` ADD `users_groups` TEXT NULL ;

ALTER TABLE `app_attachments` ADD `container` VARCHAR(16) NOT NULL DEFAULT '' ;

ALTER TABLE `app_reports` ADD `parent_entity_id` INT NOT NULL DEFAULT '0' , ADD `parent_item_id` INT NOT NULL DEFAULT '0' ;
ALTER TABLE `app_reports` ADD INDEX `idx_parent_id` (`parent_id`);
ALTER TABLE `app_reports` ADD INDEX `idx_parent_entity_id` (`parent_entity_id`);
ALTER TABLE `app_reports` ADD INDEX `idx_parent_item_id` (`parent_item_id`);