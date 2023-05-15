ALTER TABLE `#__joompush_subscribers` ADD `usergroup_id` TEXT NOT NULL AFTER `user_id`;

ALTER TABLE `#__joompush_subscriber_groups` ADD `usergroup_id` INT(11) NOT NULL AFTER `is_default`;
