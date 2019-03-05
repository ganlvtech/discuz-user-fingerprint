<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `pre_user_fingerprint_data`;
DROP TABLE IF EXISTS `pre_user_fingerprint_relation`;
EOF;

runquery($sql);

$finish = true;
