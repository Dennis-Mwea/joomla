DROP TABLE IF EXISTS `#__joompush_subscribers`;

DROP TABLE IF EXISTS `#__joompush_notification_templates`;

DROP TABLE IF EXISTS `#__joompush_configs`;

DROP TABLE IF EXISTS `#__joompush_subscriber_groups`;

DROP TABLE IF EXISTS `#__joompush_notifications`;

DROP TABLE IF EXISTS `#__joompush_subscriber_group_map`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_joompush.%');
