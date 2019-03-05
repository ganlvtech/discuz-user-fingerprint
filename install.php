<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_user_fingerprint_data` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` INT UNSIGNED NOT NULL COMMENT 'User ID',
  `username` CHAR(15) NOT NULL COMMENT 'Username',
  `sid` CHAR(6) NOT NULL COMMENT 'Session ID',
  `ua` VARCHAR(1024) NOT NULL COMMENT 'User Agent',
  `ua_md5` CHAR(32) NOT NULL COMMENT 'User Agent MD5',
  `fingerprint` CHAR(32) NOT NULL COMMENT 'Canvas Fingerprint',
  `fingerprint2` CHAR(32) NOT NULL COMMENT 'Simplified Fingerprint',
  `ip` INT UNSIGNED NOT NULL COMMENT 'IP',
  `ip2` INT UNSIGNED NOT NULL COMMENT 'IP & 0xFFFFFF00',
  `hit` INT UNSIGNED NOT NULL COMMENT 'Hit count',
  `created_at` INT UNSIGNED NOT NULL COMMENT 'Fingerprint first created time',
  `last_online_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Last online time',
  PRIMARY KEY (`id`),
  INDEX (`uid`),
  INDEX (`username`),
  INDEX (`sid`),
  INDEX (`ua_md5`),
  INDEX (`fingerprint`),
  INDEX (`fingerprint2`)
) ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `pre_user_fingerprint_relation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid1` INT UNSIGNED NOT NULL COMMENT 'User ID 1',
  `username1` CHAR(15) NOT NULL COMMENT 'Username 1',
  `uid2` INT UNSIGNED NOT NULL COMMENT 'User ID 2',
  `username2` CHAR(15) NOT NULL COMMENT 'Username 2',
  `score` INT UNSIGNED NOT NULL COMMENT 'Relation level',
  `data1` TEXT NOT NULL COMMENT 'JSON user data 1',
  `data2` TEXT NOT NULL COMMENT 'JSON user data 2',
  PRIMARY KEY (`id`),
  UNIQUE (`uid1`, `uid2`)
) ENGINE = MyISAM;
EOF;

runquery($sql);

$finish = true;
