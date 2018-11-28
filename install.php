<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_user_fingerprint_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` INT UNSIGNED NOT NULL COMMENT 'User ID',
  `username` CHAR(15) NOT NULL,
  `fingerprint` CHAR(32) NOT NULL COMMENT 'Fingerprintjs2 x64hash128 fingerprint',
  `sid` CHAR(6) NOT NULL COMMENT 'Session ID',
  `ip` INT UNSIGNED NOT NULL COMMENT 'IP',
  `ua` VARCHAR(1024) NOT NULL COMMENT 'User Agent',
  `hit` INT UNSIGNED NOT NULL COMMENT 'Hit count',
  `created_at` INT UNSIGNED NOT NULL COMMENT 'Fingerprint first created time',
  `last_online_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Last online time',
  PRIMARY KEY (`id`),
  INDEX (`sid`),
  INDEX (`fingerprint`),
  INDEX (`uid`),
  INDEX (`username`)
) ENGINE = MyISAM;
EOF;

runquery($sql);

$finish = true;
