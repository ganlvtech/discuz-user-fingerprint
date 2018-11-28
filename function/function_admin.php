<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

function user_fingerprint_admin_page($default = 1)
{
    $page = (int)$_GET['page'];
    if ($page <= 0) {
        $page = $default;
    }
    return $page;
}

function user_fingerprint_admin_per_page($default = 20)
{
    $per_page = (int)$_GET['per_page'];
    if ($per_page <= 0) {
        $per_page = $default;
    }
    return $per_page;
}

function user_fingerprint_admin_query_start($page = null, $per_page = null)
{
    if (is_null($page)) {
        $page = user_fingerprint_admin_page();
    }
    if (is_null($per_page)) {
        $per_page = user_fingerprint_admin_per_page();
    }
    return ($page - 1) * $per_page;
}

function user_fingerprint_admin_query_without_page()
{
    $vars = $_GET;
    unset($vars['page']);
    return http_build_query($vars);
}

function user_fingerprint_admin_show_stylesheet()
{
    echo '<link rel="stylesheet" href="/source/plugin/user_fingerprint/css/admin.css">';
}

function user_fingerprint_admin_show_table_subtitle() {
    showsubtitle([
        '#',
        'Fingerprint',
        'Session ID',
        'Count',
        'User ID',
        'Username',
        'IP',
        'User Agent',
        'Hit Count',
        'Created At',
        'Last Online Time',
        'Operation',
    ]);
}
