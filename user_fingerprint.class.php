<?php

use Ganlv\UserFingerprint\Libraries\Request;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/Libraries/Request.php';

class plugin_user_fingerprint
{
    public static function global_footer()
    {
        global $_G;
        if (empty($_G['uid'])) {
            return '';
        }

        $name = Request::config('sid_name', 'sid');
        $keep = Request::config('sid_keep');
        $expire = Request::config('sid_expire', 0);

        $missing = $keep ? (!isset($_COOKIE[$name]) || !$_COOKIE[$name]) : !getcookie($name);
        if ($missing) {
            dsetcookie($name, random(6), $expire, !$keep);
        }

        return '<script src="source/plugin/user_fingerprint/js/bundle.min.js" async defer></script>';
    }
}

class mobileplugin_user_fingerprint
{
    public static function global_footer_mobile()
    {
        return plugin_user_fingerprint::global_footer();
    }
}
