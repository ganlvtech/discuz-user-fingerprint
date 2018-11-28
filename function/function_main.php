<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserFingerprint;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/../Models/UserFingerprint.php';

function config($key = null, $default = null)
{
    global $_G;
    if (is_null($key)) {
        return $_G['cache']['plugin']['user_fingerprint'];
    } elseif (isset($_G['cache']['plugin']['user_fingerprint'][$key])) {
        return $_G['cache']['plugin']['user_fingerprint'][$key];
    } else {
        return $default;
    }
}

function delete_rotated()
{
    $max_log_count = (int)config('max_log_count');
    $delete_ratio = (float)config('delete_ratio');
    $table = new UserFingerprint();
    return $table->deleteRotated($max_log_count, $delete_ratio);
}

function build_response($msg = 'OK', $code = 0, $data = null)
{
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ];
}

function _($str)
{
    return lang('plugin/user_fingerprint', $str);
}
