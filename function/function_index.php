<?php

namespace Ganlv\UserFingerprint;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function build_response($msg = 'OK', $code = 0, $data = null)
{
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ];
}

function request_referer()
{
    return $_SERVER['HTTP_REFERER'];
}

function request_referer_host()
{
    $url_components = parse_url(request_referer());
    return $url_components['host'];
}

function request_server_name()
{
    return $_SERVER['SERVER_NAME'];
}

function request_is_referer_valid()
{
    return request_referer_host() === request_server_name();
}

function request_uid()
{
    global $_G;
    $uid = (int)$_G['uid'];
    return $uid;
}

function request_username()
{
    global $_G;
    $username = (string)$_G['username'];
    $username = substr($username, 0, 15);
    return $username;
}

function request_sid()
{
    $sid_name = config('sid_name', 'sid');
    if (config('sid_keep')) {
        $sid = isset($_COOKIE[$sid_name]) ? $_COOKIE[$sid_name] : '';
    } else {
        $sid = \getcookie($sid_name);
    }
    $sid = (string)$sid;
    if (1 !== preg_match('/^[0-9A-Za-z]{6}$/', $sid)) {
        return null;
    }
    return $sid;
}

function request_fingerprint()
{
    $fingerprint = (string)$_GET['fingerprint'];
    if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint)) {
        return null;
    }
    return $fingerprint;
}

function request_fingerprint2()
{
    $fingerprint2 = (string)$_GET['fingerprint2'];
    if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint2)) {
        return null;
    }
    return $fingerprint2;
}

function request_ua()
{
    $ua = (string)$_SERVER['HTTP_USER_AGENT'];
    $ua = substr($ua, 0, 1024);
    return $ua;
}

function request_ip()
{
    global $_G;
    $ip = (string)$_G['clientip'];
    $ip = ip2long($ip);
    return $ip;
}
