CREATE TABLE `offer` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT);
ALTER TABLE `offer` CHANGE `id` `offer_id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT;
ALTER TABLE `offer` ADD `offer_name` VARCHAR  NULL  DEFAULT NULL  AFTER `offer_id`;
ALTER TABLE `offer` ADD `offer_name` TEXT  NULL  AFTER `offer_id`;
ALTER TABLE `offer` ADD `offer_picture` TEXT  NULL  AFTER `offer_name`;
ALTER TABLE `offer` ADD `offer_type` TINYINT  NULL  DEFAULT NULL  AFTER `offer_picture`;
ALTER TABLE `offer` ADD `offer_description` TEXT  NULL  AFTER `offer_type`;
ALTER TABLE `offer` ADD `offer_profit` FLOAT  NULL  DEFAULT NULL  AFTER `offer_description`;
ALTER TABLE `offer` ADD `offer_link` TEXT  NULL  AFTER `offer_profit`;
ALTER TABLE `offer` ADD `offer_site_url` TEXT  NULL  AFTER `offer_link`;
ALTER TABLE `offer` CHANGE `offer_link` `offer_url` TEXT  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL;
ALTER TABLE `offer` ADD `offer_end_date` DATE  NULL  AFTER `offer_site_url`;
ALTER TABLE `offer` ADD `offer_rating` TINYINT  NULL  DEFAULT NULL  AFTER `offer_end_date`;
ALTER TABLE `offer` ADD `offer_priority` TINYINT  NULL  DEFAULT NULL  AFTER `offer_rating`;
ALTER TABLE `offer` ADD `offer_featured` TINYINT  NULL  DEFAULT NULL  AFTER `offer_priority`;
ALTER TABLE `offer` ADD `offer_status` INT  NULL  DEFAULT NULL  AFTER `offer_featured`;
ALTER TABLE `offer` CHANGE `offer_status` `offer_status` TINYINT(11)  NULL  DEFAULT '1';
ALTER TABLE `offer` ADD `offer_views` INT  NULL  DEFAULT NULL  AFTER `offer_status`;
ALTER TABLE `offer` CHANGE `offer_type` `offer_type` TINYINT(4)  NULL  DEFAULT NULL  COMMENT '1-AirDrop, 2-Bounty, 3-ICO';
ALTER TABLE `offer` ADD `offer_delete` TINYINT  NULL  DEFAULT NULL  AFTER `offer_views`;
ALTER TABLE `offer` CHANGE `offer_delete` `offer_delete` TINYINT(4)  NULL  DEFAULT '0';



ALTER TABLE `offer` ADD `offer_code` FLOAT  NULL  DEFAULT NULL  AFTER `offer_delete`;
ALTER TABLE `offer` CHANGE `offer_code` `offer_code` VARCHAR(4)  NULL  DEFAULT NULL;
