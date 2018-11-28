<?php

namespace Ganlv\UserFingerprint;

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once __DIR__ . '/function_main.php';

function admin_page($default = 1)
{
    $page = (int)$_GET['page'];
    if ($page <= 0) {
        $page = $default;
    }
    return $page;
}

function admin_per_page($default = 20)
{
    $per_page = (int)$_GET['per_page'];
    if ($per_page <= 0) {
        $per_page = $default;
    }
    return $per_page;
}

function admin_query_start($page = null, $per_page = null)
{
    if (is_null($page)) {
        $page = admin_page();
    }
    if (is_null($per_page)) {
        $per_page = admin_per_page();
    }
    return ($page - 1) * $per_page;
}

function admin_query_without_page()
{
    $vars = $_GET;
    unset($vars['page']);
    return http_build_query($vars);
}

function admin_show_paginator($count, $page = null, $per_page = null)
{
    if (is_null($page)) {
        $page = admin_page();
    }
    if (is_null($per_page)) {
        $per_page = admin_per_page();
    }
    $mpurl = ADMINSCRIPT . '?' . admin_query_without_page();
    $multipage = multi($count, $per_page, $page, $mpurl);
    echo $multipage;
}

function admin_show_stylesheet()
{
    echo <<<'EOD'
<style>
    .user-fingerprint-row-title td {
        background-color: #ccc !important;
    }
</style>
EOD;
}

function admin_show_table_subtitle()
{
    showsubtitle([
        _('#'),
        _('Fingerprint'),
        _('Session ID'),
        _('Count'),
        _('User ID'),
        _('Username'),
        _('IP'),
        _('User Agent'),
        _('Hit Count'),
        _('Created At'),
        _('Last Online Time'),
        _('Operation'),
    ]);
}

function admin_show_table_row_title($item)
{
    showtablerow('class="user-fingerprint-row-title"', [], dhtmlspecialchars([
        '-',
        isset($item['fingerprint']) ? $item['fingerprint'] : '-',
        isset($item['sid']) ? $item['sid'] : '-',
        $item['count'],
        isset($item['uid']) ? $item['uid'] : '-',
        isset($item['username']) ? $item['username'] : '-',
        '-',
        '-',
        $item['hit'],
        '-',
        '-',
        '-',
    ]));
}

function admin_show_table_row_content($row, $item)
{
    showtablerow('', [
        '',
        'title="' . dhtmlspecialchars($row['fingerprint']) . '"',
        'title="' . dhtmlspecialchars($row['sid']) . '"',
        '-',
        'title="' . dhtmlspecialchars($row['uid']) . '"',
        'title="' . dhtmlspecialchars($row['username']) . '"',
        'title="' . dhtmlspecialchars(convertip(long2ip($row['ip']))) . '"',
        'title="' . dhtmlspecialchars($row['ua']) . '"',
        'title="' . dhtmlspecialchars($row['hit']) . '"',
        'title="' . dhtmlspecialchars(date('Y-m-d H:i:s', $row['created_at'])) . '"',
        'title="' . dhtmlspecialchars(date('Y-m-d H:i:s', $row['last_online_time'])) . '"',
        '',
    ], [
        dhtmlspecialchars($row['id']),
        dhtmlspecialchars(isset($item['fingerprint']) ? '-' : $row['fingerprint']),
        dhtmlspecialchars(isset($item['sid']) ? '-' : $row['sid']),
        dhtmlspecialchars('-'),
        dhtmlspecialchars(isset($item['uid']) ? '-' : $row['uid']),
        dhtmlspecialchars(isset($item['uid']) ? '-' : $row['username']),
        dhtmlspecialchars(long2ip($row['ip'])),
        dhtmlspecialchars(substr($row['ua'], 0, 32) . '...'),
        dhtmlspecialchars($row['hit']),
        dhtmlspecialchars(date('m-d H:i', $row['created_at'])),
        dhtmlspecialchars(date('m-d H:i', $row['last_online_time'])),
        '<a target="_blank" rel="noopener" href="' . ADMINSCRIPT . '?frames=yes&action=members&operation=search&submit=1&uid=' . $row['uid'] . '">' . _('Find user') . '</a> '
        . '<a target="_blank" rel="noopener" href="home.php?mod=space&uid=' . $row['uid'] . '">' . _('User space') . '</a>',
    ]);
}
