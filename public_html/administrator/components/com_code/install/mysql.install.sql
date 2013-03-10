CREATE TABLE IF NOT EXISTS `jos_code_activity_types` (
  `activity_type` tinyint(4) default NULL COMMENT '1-create; 2-comment; 3-change; 4-test; 5-patch; 6-pull in comment; 7-pull in description',
  `activity_title` varchar(255) default NULL COMMENT 'Title for each type',
  `activity_group` varchar(255) default NULL COMMENT 'Tracker, Test, Code',
  `activity_description` varchar(500) default NULL COMMENT 'Activity description',
  `activity_points` tinyint(4) default NULL COMMENT 'Weighting for each type of activity',
  PRIMARY KEY  (`activity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `jos_code_activity_types` 
(`activity_type`, `activity_title`, `activity_group`, `activity_description`, `activity_points`) VALUES
(1, 'Create Issue', 'Tracker', 'Create a new issue in tracker.', 3),
(2, 'Comment Issue', 'Tracker', 'Add a comment to an issue.', 1),
(3, 'Change Issue', 'Tracker', 'Change the status of an issue.', 1),
(4, 'Test Issue', 'Test', 'Test an issue.', 5),
(5, 'Patch Issue', 'Code', 'Create a patch or diff file for an issue.', 5),
(6, 'Pull Request in Comment', 'Code', 'Add a pull request link in a comment.', 5),
(7, 'Pull Request in Description', 'Code', 'Add a pull request link in the original issue description.', 5);

CREATE TABLE IF NOT EXISTS `jos_code_activity_detail` (
  `activity_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1-create; 2-comment; 3-change; 4-test; 5-patch; 6-pull in comment; 7-pull in description',
  `activity_xref_id` int(10) unsigned NOT NULL COMMENT 'id for issue, response, change, or file',
  `user_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL,
  PRIMARY KEY (`activity_type`,`activity_xref_id`),
  KEY `idx_activity_date` (`activity_date`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_jc_issue_id` (`jc_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `jos_code_branches` (
  `branch_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `asset_id` int(11) NOT NULL,
  `user_id` int(10) unsigned default NULL,
  `path` varchar(128) default NULL,
  `title` varchar(255) default NULL,
  `summary` varchar(512) NOT NULL,
  `description` text,
  `access` int(11) NOT NULL,
  `published` tinyint(4) default NULL,
  `updated_date` datetime default NULL,
  `created_date` datetime default NULL,
  `last_build_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`branch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_builds` (
  `build_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) unsigned DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `log` varchar(512) DEFAULT NULL,
  `changelog` mediumtext,
  `published` tinyint(4) DEFAULT NULL,
  `commit_date` datetime DEFAULT NULL,
  `ut_tests` int(10) unsigned DEFAULT NULL,
  `ut_assertions` int(10) unsigned DEFAULT NULL,
  `ut_failures` int(10) unsigned DEFAULT NULL,
  `ut_errors` int(10) unsigned DEFAULT NULL,
  `ut_pass_pct` float DEFAULT NULL,
  `ut_fail_pct` float DEFAULT NULL,
  `ut_error_pct` float DEFAULT NULL,
  `ut_report` mediumtext,
  `ut_delta` mediumtext,
  `st_tests` int(10) unsigned DEFAULT NULL,
  `st_assertions` int(10) unsigned DEFAULT NULL,
  `st_failures` int(10) unsigned DEFAULT NULL,
  `st_errors` int(10) unsigned DEFAULT NULL,
  `st_pass_pct` float DEFAULT NULL,
  `st_fail_pct` float DEFAULT NULL,
  `st_error_pct` float DEFAULT NULL,
  `st_report` mediumtext,
  `st_delta` mediumtext,
  `loc` int(10) unsigned DEFAULT NULL,
  `loc_covered` int(10) unsigned DEFAULT NULL,
  `loc_covered_pct` float DEFAULT NULL,
  `methods` int(10) unsigned DEFAULT NULL,
  `methods_covered` int(10) unsigned DEFAULT NULL,
  `methods_covered_pct` float DEFAULT NULL,
  PRIMARY KEY (`build_id`),
  KEY `idx_branch_revision` (`branch_id`,`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_nightly_builds` (
  `build_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `changelog` mediumtext,
  `published` tinyint(4) DEFAULT NULL,
  `build_date` datetime DEFAULT NULL,
  `ut_tests` int(10) unsigned DEFAULT NULL,
  `ut_assertions` int(10) unsigned DEFAULT NULL,
  `ut_failures` int(10) unsigned DEFAULT NULL,
  `ut_errors` int(10) unsigned DEFAULT NULL,
  `ut_pass_pct` float DEFAULT NULL,
  `ut_fail_pct` float DEFAULT NULL,
  `ut_error_pct` float DEFAULT NULL,
  `ut_report` mediumtext,
  `ut_delta` mediumtext,
  `st_tests` int(10) unsigned DEFAULT NULL,
  `st_assertions` int(10) unsigned DEFAULT NULL,
  `st_failures` int(10) unsigned DEFAULT NULL,
  `st_errors` int(10) unsigned DEFAULT NULL,
  `st_pass_pct` float DEFAULT NULL,
  `st_fail_pct` float DEFAULT NULL,
  `st_error_pct` float DEFAULT NULL,
  `st_report` mediumtext,
  `st_delta` mediumtext,
  `loc` int(10) unsigned DEFAULT NULL,
  `loc_covered` int(10) unsigned DEFAULT NULL,
  `loc_covered_pct` float DEFAULT NULL,
  `methods` int(10) unsigned DEFAULT NULL,
  `methods_covered` int(10) unsigned DEFAULT NULL,
  `methods_covered_pct` float DEFAULT NULL,
  PRIMARY KEY (`build_id`),
  KEY `idx_branch_revision` (`branch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_projects` (
  `project_id` int(10) unsigned NOT NULL auto_increment,
  `asset_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `summary` varchar(512) NOT NULL,
  `description` text NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `jc_project_id` int(11) default NULL,
  PRIMARY KEY  (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tags` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(512) DEFAULT NULL,
  `total` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `idx_code_tags_total` (`total`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_assignments` (
  `issue_id` int(10) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `jc_user_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  KEY `issue_id` (`issue_id`,`user_id`),
  KEY `jc_issue_id` (`jc_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_changes` (
  `change_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int(10) unsigned NOT NULL,
  `tracker_id` int(10) unsigned NOT NULL,
  `change_date` datetime NOT NULL,
  `change_by` int(11) NOT NULL,
  `data` text NOT NULL,
  `jc_change_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  `jc_tracker_id` int(11) DEFAULT NULL,
  `jc_change_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`change_id`),
  UNIQUE KEY `jc_change_id` (`jc_change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_commits` (
  `commit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int(10) unsigned NOT NULL,
  `tracker_id` int(10) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `message` text NOT NULL,
  `jc_commit_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  `jc_tracker_id` int(11) DEFAULT NULL,
  `jc_created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`commit_id`),
  UNIQUE KEY `jc_commit_id` (`jc_commit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int(10) unsigned NOT NULL,
  `tracker_id` int(10) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `jc_file_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  `jc_tracker_id` int(11) DEFAULT NULL,
  `jc_created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `idx_issue_files_legacy` (`jc_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_responses` (
  `response_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` int(10) unsigned NOT NULL,
  `tracker_id` int(10) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `body` text NOT NULL,
  `jc_response_id` int(11) DEFAULT NULL,
  `jc_issue_id` int(11) DEFAULT NULL,
  `jc_tracker_id` int(11) DEFAULT NULL,
  `jc_created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`response_id`),
  UNIQUE KEY `idx_tracker_responses_legacy` (`jc_response_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issue_tag_map` (
  `issue_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(10) unsigned DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_tracker_issues` (
  `issue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tracker_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `build_id` int(10) unsigned DEFAULT NULL,
  `state` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `close_date` datetime NOT NULL,
  `close_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `jc_issue_id` int(11) DEFAULT NULL,
  `jc_tracker_id` int(11) DEFAULT NULL,
  `jc_project_id` int(11) DEFAULT NULL,
  `jc_created_by` int(11) DEFAULT NULL,
  `jc_modified_by` int(11) DEFAULT NULL,
  `jc_close_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`issue_id`),
  UNIQUE KEY `idx_tracker_issues_legacy` (`jc_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_trackers` (
  `tracker_id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `asset_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `summary` varchar(512) NOT NULL,
  `description` text NOT NULL,
  `state` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `options` text NOT NULL,
  `metadata` text NOT NULL,
  `item_count` int(11) NOT NULL,
  `open_item_count` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `jc_tracker_id` int(11) default NULL,
  `jc_project_id` int(11) default NULL,
  PRIMARY KEY  (`tracker_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `jos_code_users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address` varchar(512) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(5) NOT NULL,
  `postal_code` varchar(25) NOT NULL,
  `longitude` float NOT NULL,
  `latitude` float NOT NULL,
  `phone` varchar(255) NOT NULL,
  `agreed_tos` int(1) unsigned NOT NULL,
  `jca_document_id` varchar(255) NOT NULL,
  `signed_jca` int(1) unsigned NOT NULL,
  `jc_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

