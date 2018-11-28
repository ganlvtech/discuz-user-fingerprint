<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserFingerprint;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/Models/UserFingerprint.php';
require_once __DIR__ . '/function/function_main.php';

function main()
{
    global $_G;

    if (!$_SERVER['HTTP_REFERER']) {
        return build_response(_('Empty referer.'), 1);
    }

    $url_components = parse_url($_SERVER['HTTP_REFERER']);
    if ($url_components['host'] !== $_SERVER['SERVER_NAME']) {
        return build_response(_('Invalid referer.'), 2);
    }

    $uid = (int)$_G['uid'];
    if ($uid <= 0) {
        return build_response(_('User not login.'), 3);
    }

    $sid = (string)getcookie('sid');
    if (1 !== preg_match('/^[0-9A-Za-z]{6}$/', $sid)) {
        return build_response(_('No valid session id exists.'), 4);
    }

    $fingerprint = (string)getgpc('fingerprint');
    if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint)) {
        return build_response(_('Invalid fingerprint.'), 5);
    }

    $table = new UserFingerprint;
    $record = $table->fetchIdHitByUidSidFingerprint($uid, $sid, $fingerprint);
    if ($record) {
        $table->touchById($record['id']);
        return build_response(_('User record exists. Last online time updated.'));
    }

    $username = (string)$_G['username'];
    $ua = (string)$_SERVER['HTTP_USER_AGENT'];
    $ip = (string)$_G['clientip'];
    $table->insertData(compact('fingerprint', 'sid', 'uid', 'username', 'ip', 'ua'));

    delete_rotated();

    return build_response(_('User record inserted.'));
}

$result = main();
header('Content-Type: text/javascript; charset=UTF-8');
// if (DISCUZ_DEBUG) {
    echo 'console.log(', json_encode($result), ');';
// }
