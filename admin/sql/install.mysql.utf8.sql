--
-- com_books_list - install schema
--

CREATE TABLE IF NOT EXISTS `#__booklist_books` (
  `id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `asset_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `title`          VARCHAR(255) NOT NULL DEFAULT '',
  `subtitle`       VARCHAR(255) NOT NULL DEFAULT '',
  `alias`          VARCHAR(255) NOT NULL DEFAULT '',
  `catid`          INT(11)      NOT NULL DEFAULT 0,
  `editor_id`      INT(11)      NOT NULL DEFAULT 0,
  `isbn`           VARCHAR(32)  NOT NULL DEFAULT '',
  `issn`           VARCHAR(32)  NOT NULL DEFAULT '',
  `year`           INT(4)       NOT NULL DEFAULT 0,
  `pages`          INT(11)      NOT NULL DEFAULT 0,
  `language_book`  VARCHAR(64)  NOT NULL DEFAULT '',
  `price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `description`    MEDIUMTEXT   DEFAULT NULL,
  `image`          VARCHAR(255) NOT NULL DEFAULT '',
  `file`           VARCHAR(255) NOT NULL DEFAULT '',
  `url`            VARCHAR(255) NOT NULL DEFAULT '',
  `url_label`      VARCHAR(255) NOT NULL DEFAULT '',
  `hits`           INT(11)      NOT NULL DEFAULT 0,
  `vote_sum`       INT(11)      NOT NULL DEFAULT 0,
  `vote_count`     INT(11)      NOT NULL DEFAULT 0,
  `state`          TINYINT(3)   NOT NULL DEFAULT 0,
  `access`         INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `ordering`       INT(11)      NOT NULL DEFAULT 0,
  `language`       CHAR(7)      NOT NULL DEFAULT '*',
  `metakey`        TEXT         DEFAULT NULL,
  `metadesc`       TEXT         DEFAULT NULL,
  `metadata`       TEXT         DEFAULT NULL,
  `created`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `modified`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out`    INT(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME   DEFAULT NULL,
  `publish_up`     DATETIME     DEFAULT NULL,
  `publish_down`   DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_editor` (`editor_id`),
  KEY `idx_access` (`access`),
  KEY `idx_language` (`language`),
  KEY `idx_alias` (`alias`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__booklist_authors` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `lastname`     VARCHAR(255) NOT NULL DEFAULT '',
  `name`         VARCHAR(255) NOT NULL DEFAULT '',
  `alias`        VARCHAR(255) NOT NULL DEFAULT '',
  `image`        VARCHAR(255) NOT NULL DEFAULT '',
  `description`  MEDIUMTEXT   DEFAULT NULL,
  `state`        TINYINT(1)   NOT NULL DEFAULT 0,
  `ordering`     INT(11)      NOT NULL DEFAULT 0,
  `access`       INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `language`     CHAR(7)      NOT NULL DEFAULT '*',
  `checked_out`  INT(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_lastname` (`lastname`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__booklist_editors` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(255) NOT NULL DEFAULT '',
  `alias`        VARCHAR(255) NOT NULL DEFAULT '',
  `description`  MEDIUMTEXT   DEFAULT NULL,
  `state`        TINYINT(1)   NOT NULL DEFAULT 0,
  `ordering`     INT(11)      NOT NULL DEFAULT 0,
  `access`       INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `language`     CHAR(7)      NOT NULL DEFAULT '*',
  `checked_out`  INT(10) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__booklist_book_author` (
  `id`        INT(11) NOT NULL AUTO_INCREMENT,
  `book_id`   INT(11) NOT NULL DEFAULT 0,
  `author_id` INT(11) NOT NULL DEFAULT 0,
  `ordering`  INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_book` (`book_id`),
  KEY `idx_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
