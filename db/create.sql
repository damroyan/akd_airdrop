-- Create syntax for TABLE 'action_token'
CREATE TABLE `action_token` (
  `action_token_id` varchar(64) NOT NULL DEFAULT '',
  `action_token_action` text,
  `action_token_params` text,
  `action_token_status` tinyint(1) unsigned DEFAULT '1',
  `action_token_ip` varchar(255) DEFAULT NULL,
  `action_token_cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `action_token_edate` timestamp NULL DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`action_token_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `action_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'company'
CREATE TABLE `company` (
  `company_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(256) DEFAULT NULL,
  `company_logo` text,
  `company_description` text,
  `company_cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_mdate` timestamp NULL DEFAULT NULL,
  `company_status` varchar(30) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'email'
CREATE TABLE `email` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_from` text NOT NULL,
  `email_to` text NOT NULL,
  `email_subject` text NOT NULL,
  `email_content` longtext NOT NULL,
  `email_header` text,
  `email_label` text,
  `email_priority` int(10) unsigned DEFAULT '0',
  `email_cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email_mdate` timestamp NULL DEFAULT NULL,
  `email_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email_failed` text,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'email_blacklist'
CREATE TABLE `email_blacklist` (
  `email_blacklist_id` varchar(32) NOT NULL DEFAULT '',
  `email_blacklist_email` text,
  `email_blacklist_cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `email_blacklist_ddate` timestamp NULL DEFAULT NULL,
  `email_blacklist_status` tinyint(1) unsigned DEFAULT '0',
  `email_blacklist_count` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'feedback'
CREATE TABLE `feedback` (
  `feedback_id` int(11) unsigned NOT NULL,
  `feedback_type` varchar(30) DEFAULT NULL,
  `feedback_user_name` text,
  `feedback_description` text,
  `feedback_email` varchar(256) DEFAULT NULL,
  `feedback_phone` varchar(100) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `feedback_moderator_user_id` int(10) unsigned DEFAULT NULL,
  `feedback_moderator_mdate` timestamp NULL DEFAULT NULL,
  `feedback_cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `feedback_status` varchar(30) DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'message'
CREATE TABLE `message` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_role` varchar(10) DEFAULT NULL,
  `message_text` text,
  `message_date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message_type` varchar(10) DEFAULT 'warning',
  `message_frequency` varchar(10) NOT NULL DEFAULT 'global',
  `message_status` varchar(10) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'user'
CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_password` text,
  `user_name` text,
  `user_firstname` text,
  `user_lastname` text,
  `user_picture` text,
  `user_lang` varchar(2) DEFAULT NULL,
  `user_role` text,
  `user_cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_mdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_delete` tinyint(1) unsigned DEFAULT '0',
  `user_bad_logins` int(11) NOT NULL DEFAULT '0',
  `user_newsletter_subscription_date` timestamp NULL DEFAULT NULL,
  `user_status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
INSERT INTO `user` (`user_id`, `user_login`, `user_email`, `user_password`, `user_name`, `user_firstname`, `user_lastname`, `user_picture`, `user_lang`, `user_role`, `user_cdate`, `user_mdate`, `user_delete`, `user_bad_logins`,  `user_newsletter_subscription_date`,  `user_status`)
VALUES
	(1, 'admin@skeleton.com', 'admin@skeleton.com', '$2y$08$Zy80N0NvREUvbFUvRzB5VOvXeBBmvhnbui12hRueHQlIN9H7DVeLC', 'Mega Admin', 'Admin', 'Admin', '', 'en', 'admin', '2016-11-03 14:15:16', '2017-05-12 17:01:06', 0, 0, '2016-11-03 14:15:16', 1);


-- Create syntax for TABLE 'user_access_log'
CREATE TABLE `user_access_log` (
  `user_access_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `user_access_log_cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_access_log_ip` varchar(255) DEFAULT '',
  `user_access_log_ua` text,
  PRIMARY KEY (`user_access_log_id`),
  KEY `user_id` (`user_id`),
  KEY `user_access_log_cdate` (`user_access_log_cdate`),
  KEY `user_access_log_ip` (`user_access_log_ip`),
  CONSTRAINT `user_access_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'user_member'
CREATE TABLE `user_member` (
  `user_member_id` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned DEFAULT NULL,
  `user_member_cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_member_mdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_member_ip` varchar(255) DEFAULT NULL,
  `user_member_ua` text,
  PRIMARY KEY (`user_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'user_message'
CREATE TABLE `user_message` (
  `user_message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_message_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_message_id`),
  UNIQUE KEY `message_id` (`message_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'user_token'
CREATE TABLE `user_token` (
  `user_token_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `user_token_type` varchar(32) DEFAULT NULL,
  `user_token_expired` timestamp NULL DEFAULT NULL,
  `user_token_value` text,
  `user_token_params` text,
  `user_token_cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_token_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;