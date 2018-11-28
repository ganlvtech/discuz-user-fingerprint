<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('USER_FINGERPRINT_LOG_TABLE_NAME', 'user_fingerprint_log');
define('USER_FINGERPRINT_IS_RETURN', false);

function user_fingerprint_clear_old_logs()
{
    global $_G;
    $max_log_count = (int)$_G['cache']['plugin']['user_fingerprint']['max_log_count'];
    if ($max_log_count <= 0) {
        return null;
    }
    $table = DB::table(USER_FINGERPRINT_LOG_TABLE_NAME);
    $log_count = DB::fetch_first("SELECT COUNT(*) AS `count` FROM `$table`");
    $log_count = (int)$log_count['count'];
    if ($log_count <= $max_log_count) {
        return null;
    }
    $delete_ratio = (float)$_G['cache']['plugin']['user_fingerprint']['delete_ratio'];
    if ($delete_ratio <= 0 || $delete_ratio > 0.5) {
        $delete_ratio = 0.01;
    }
    $delete_log_count = (int)($log_count * $delete_ratio);
    $limit = $log_count - $delete_log_count;
    $delete_id = DB::fetch_first("SELECT `id` FROM `$table` ORDER BY `id` DESC LIMIT $limit, 1");
    $delete_id = (int)$delete_id['id'];
    $delete_id_quoted = DB::quote($delete_id);
    return DB::delete(USER_FINGERPRINT_LOG_TABLE_NAME, "`id` <= $delete_id_quoted");
}

function user_fingerprint_log_insert()
{
    global $_G;

    $uid = (int)$_G['uid'];
    if ($uid <= 0) {
        return [
            'code' => 1,
            'msg' => 'User not login.',
        ];
    }

    $sid = getcookie('sid');
    $sid = (string)$sid;
    if (1 !== preg_match('/^[0-9A-Za-z]{6}$/', $sid)) {
        return [
            'code' => 2,
            'msg' => 'No valid session id exists.',
        ];
    }

    $fingerprint = getgpc('fingerprint');
    $fingerprint = (string)$fingerprint;
    if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint)) {
        return [
            'code' => 3,
            'msg' => 'Invalid fingerprint.',
        ];
    }

    $table = DB::table(USER_FINGERPRINT_LOG_TABLE_NAME);
    $uid_quoted = DB::quote($uid);
    $sid_quoted = DB::quote($sid);
    $fingerprint_quoted = DB::quote($fingerprint);
    $record = DB::fetch_first("SELECT `id`, `hit` FROM `$table` WHERE `uid` = $uid_quoted AND `sid` = $sid_quoted AND `fingerprint` = $fingerprint_quoted LIMIT 1");
    if ($record) {
        $record_id = (int)$record['id'];
        $record_id_encoded = DB::quote($record_id);
        $record_hit = (int)$record['hit'];
        ++$record_hit;
        DB::update(USER_FINGERPRINT_LOG_TABLE_NAME, [
            'last_online_time' => TIMESTAMP,
            'hit' => $record_hit,
        ], "`id` = $record_id_encoded");
        return [
            'code' => 0,
            'msg' => 'OK',
            'data' => 'User record exists, last online time updated.',
        ];
    }

    $username = (string)$_G['username'];
    $ua = (string)$_SERVER['HTTP_USER_AGENT'];
    $ip = (string)$_G['clientip'];
    DB::insert(USER_FINGERPRINT_LOG_TABLE_NAME, [
        'fingerprint' => substr($fingerprint, 0, 32),
        'sid' => substr($sid, 0, 6),
        'uid' => $uid,
        'username' => $username,
        'ip' => ip2long($ip),
        'ua' => substr($ua, 0, 1024),
        'hit' => 1,
        'created_at' => TIMESTAMP,
        'last_online_time' => TIMESTAMP,
    ]);

    user_fingerprint_clear_old_logs();

    return [
        'code' => 0,
        'msg' => 'OK',
        'data' => 'User record inserted.',
    ];
}

$user_fingerprint_result = user_fingerprint_log_insert();
if (USER_FINGERPRINT_IS_RETURN) {
    echo 'console.log(', json_encode($user_fingerprint_result), ');';
}
