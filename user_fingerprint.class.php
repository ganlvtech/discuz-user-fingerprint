<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_user_fingerprint
{
    public static function global_footer()
    {
        global $_G;
        if (empty($_G['uid'])) {
            return '';
        }
        if (empty($_G['sid'])) {
            dsetcookie('sid', random(6));
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
