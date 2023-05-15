CREATE TABLE IF NOT EXISTS `#__joompush_subscribers` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL ,
`key` VARCHAR(255)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`usergroup_id` TEXT  NOT NULL ,
`browser` VARCHAR(50)  NOT NULL ,
`type` VARCHAR(50) NOT NULL,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Subscriber','com_joompush.subscriber','{"special":{"dbtable":"#__joompush_subscribers","key":"id","type":"Subscriber","prefix":"JoompushTable"}}', '{"formFile":"administrator\/components\/com_joompush\/models\/forms\/subscriber.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_joompush.subscriber')
) LIMIT 1;

CREATE TABLE IF NOT EXISTS `#__joompush_notification_templates` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`message` TEXT NOT NULL ,
`icon` VARCHAR(255)  NOT NULL ,
`url` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Notification Template','com_joompush.notificationtemplate','{"special":{"dbtable":"#__joompush_notification_templates","key":"id","type":"Notificationtemplate","prefix":"JoompushTable"}}', '{"formFile":"administrator\/components\/com_joompush\/models\/forms\/notificationtemplate.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"message"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_joompush.notificationtemplate')
) LIMIT 1;

CREATE TABLE IF NOT EXISTS `#__joompush_configs` (
  `id` int(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__joompush_subscriber_group_map` (
  `id` int(11) UNSIGNED NOT NULL  AUTO_INCREMENT,
  `subscriber_id` INT(11)  NOT NULL ,
  `group_id` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__joompush_subscriber_groups` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`description` TEXT NOT NULL ,
`is_default` INT NOT NULL,
`usergroup_id` INT NOT NULL,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Subscriber Group','com_joompush.subscribergroup','{"special":{"dbtable":"#__joompush_subscriber_groups","key":"id","type":"Subscribergroup","prefix":"JoompushTable"}}', '{"formFile":"administrator\/components\/com_joompush\/models\/forms\/subscribergroup.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"description"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_joompush.subscribergroup')
) LIMIT 1;


CREATE TABLE IF NOT EXISTS `#__joompush_notifications` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`subscriber_id` int(11) NOT NULL,
`key` varchar(255) NOT NULL,
`group_id` int(11) NOT NULL,
`type` varchar(50) NOT NULL,
`title` varchar(255) NOT NULL,
`message` text NOT NULL,
`icon` varchar(255) NOT NULL,
`url` varchar(255) NOT NULL,
`sent` tinyint(1) NOT NULL,
`isread` int(11) NOT NULL,
`sent_by` int(11) NOT NULL,
`sent_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`client` varchar(255) NOT NULL,
`client_id` int(11) NOT NULL,
`code` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;
