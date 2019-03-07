<?php

use function Ganlv\UserFingerprint\config;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/function/function_core.php';

class plugin_user_fingerprint
{
    public static function global_footer()
    {
        global $_G;
        if (empty($_G['uid'])) {
            return '';
        }

        $name = config('sid_name', 'sid');
        $keep = config('sid_keep');
        $expire = config('sid_expire', 0);

        // Discuz! 在退出登录时会移除指定前缀开头的 Cookies
        // 如果希望退出时保留 Cookies ($keep == true)，这里读写无前缀的 Cookies
        $missing = $keep ? (!isset($_COOKIE[$name]) || !$_COOKIE[$name]) : !getcookie($name);
        if ($missing) {
            dsetcookie($name, random(6), $expire, !$keep);
        }

		if (!config('js_path')) return;
        $js_path = config('js_path', 'source/plugin/user_fingerprint/js/dist/index.min.js');
        return '<script src="' . $js_path . '" async defer></script>';
    }
}

class mobileplugin_user_fingerprint
{
    public static function global_footer_mobile()
    {
        return plugin_user_fingerprint::global_footer();
    }
}
