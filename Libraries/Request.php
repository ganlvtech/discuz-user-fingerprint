<?php

namespace Ganlv\UserFingerprint\Libraries;

class Request
{
    const IP_MASK = 0xffffff00;

    public static function config($key = null, $default = null)
    {
        global $_G;
        if (is_null($key)) {
            return $_G['cache']['plugin']['user_fingerprint'];
        } elseif (!empty($_G['cache']['plugin']['user_fingerprint'][$key])) {
            return $_G['cache']['plugin']['user_fingerprint'][$key];
        } else {
            return $default;
        }
    }

    public static function referer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public static function refererHost()
    {
        $url_components = parse_url(self::referer());
        return $url_components['host'];
    }

    public static function serverName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    public static function isRefererValid()
    {
        return self::refererHost() === self::serverName();
    }

    public static function uid()
    {
        global $_G;
        $uid = (int)$_G['uid'];
        return $uid;
    }

    public static function username()
    {
        global $_G;
        $username = (string)$_G['username'];
        $username = substr($username, 0, 15);
        return $username;
    }

    public static function sid()
    {
        $sid_name = self::config('sid_name', 'sid');
        if (self::config('sid_keep')) {
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

    public static function fingerprint()
    {
        $fingerprint = (string)$_GET['fingerprint'];
        if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint)) {
            return null;
        }
        return $fingerprint;
    }

    public static function fingerprint2()
    {
        $fingerprint2 = (string)$_GET['fingerprint2'];
        if (1 !== preg_match('/^[0-9a-f]{32}$/', $fingerprint2)) {
            return null;
        }
        return $fingerprint2;
    }

    public static function ua()
    {
        $ua = (string)$_SERVER['HTTP_USER_AGENT'];
        $ua = substr($ua, 0, 1024);
        return $ua;
    }

    public static function ip()
    {
        global $_G;
        $ip = (string)$_G['clientip'];
        $ip = ip2long($ip);
        return $ip;
    }
}
