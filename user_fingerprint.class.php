<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_user_fingerprint
{
    public static function global_footer()
    {
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
