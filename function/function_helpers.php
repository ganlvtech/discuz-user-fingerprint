<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/../class/table/table_user_fingerprint.php';

function user_fingerprint_delete_rotated()
{
    global $_G;
    $max_log_count = (int)$_G['cache']['plugin']['user_fingerprint']['max_log_count'];
    $delete_ratio = (float)$_G['cache']['plugin']['user_fingerprint']['delete_ratio'];
    $table = new table_user_fingerprint();
    return $table->delete_rotated($max_log_count, $delete_ratio);
}

function user_fingerprint_lang($langvar)
{
    return lang('plugin/user_fingerprint', $langvar);
}
