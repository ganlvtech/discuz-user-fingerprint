<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserRelation;

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once __DIR__ . '/Models/UserRelation.php';

function _($str)
{
    return lang('plugin/user_fingerprint', $str);
}

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

function admin_query_without_page()
{
    $vars = $_GET;
    unset($vars['page']);
    return http_build_query($vars);
}

function human_readable_data($data)
{
    $data['ip'] = long2ip($data['ip']);
    $data['ip2'] = long2ip($data['ip2']);
    $data['ip_geo'] = convertip($data['ip']);
    $data['created_at'] = date('Y-m-d H:i:s', $data['created_at']);
    $data['last_online_time'] = date('Y-m-d H:i:s', $data['last_online_time']);
    return $data;
}

function human_readable_text($data)
{
    return _('User ID') . ": {$data['uid']}\n"
        . _('Username') . ": {$data['username']}\n"
        . _('Session ID') . ": {$data['sid']}\n"
        . _('User Agent') . ": {$data['ua']}\n"
        . _('Fingerprint') . ": {$data['fingerprint']}\n"
        . _('Simplified Fingerprint') . ": {$data['fingerprint2']}\n"
        . _('IP') . ": {$data['ip']}\n"
        . _('IP Geo Location') .": {$data['ip_geo']}\n"
        . _('IP Masked') . ": {$data['ip2']}\n"
        . _('Hit') . ": {$data['hit']}\n"
        . _('Created At') . ": {$data['created_at']}\n"
        . _('Last Online Time') .": {$data['last_online_time']}\n";
}

$page = admin_page();
$per_page = admin_per_page();

showtableheader(_('User Relation Top'));
showsubtitle([
    _('#'),
    _('User ID') . ' 1',
    _('Username') . ' 1',
    _('IP') . ' 1',
    _('Detail') . ' 1',
    _('User ID') . ' 2',
    _('Username') . ' 2',
    _('IP') . ' 2',
    _('Detail') . ' 2',
    _('Relation Score'),
]);
$userRelation = new UserRelation();
$records = $userRelation->fetchAll($page, $per_page);
foreach ($records as $row) {
    $data1 = human_readable_data($row['data1']);
    $data2 = human_readable_data($row['data2']);
    showtablerow('', [
        '',
        '',
        '',
        'title="' . dhtmlspecialchars($data1['ip_geo']) . '"',
        'title="' . dhtmlspecialchars(human_readable_text($data1)) . '"',
        '',
        '',
        'title="' . dhtmlspecialchars($data2['ip_geo']) . '"',
        'title="' . dhtmlspecialchars(human_readable_text($data2)) . '"',
        '',
    ], [
        dhtmlspecialchars($row['id']),
        '<a target="_blank" rel="noopener" href="' . ADMINSCRIPT . '?frames=yes&action=members&operation=search&submit=1&uid=' . $row['uid1'] . '">' . dhtmlspecialchars($row['uid1']) . '</a>',
        '<a target="_blank" rel="noopener" href="home.php?mod=space&uid=' . $row['uid1'] . '">' . dhtmlspecialchars($row['username1']) . '</a>',
        dhtmlspecialchars($data1['ip']),
        dhtmlspecialchars(_('Hover')),
        '<a target="_blank" rel="noopener" href="' . ADMINSCRIPT . '?frames=yes&action=members&operation=search&submit=1&uid=' . $row['uid2'] . '">' . dhtmlspecialchars($row['uid2']) . '</a>',
        '<a target="_blank" rel="noopener" href="home.php?mod=space&uid=' . $row['uid2'] . '">' . dhtmlspecialchars($row['username2']) . '</a>',
        dhtmlspecialchars($data2['ip']),
        dhtmlspecialchars(_('Hover')),
        dhtmlspecialchars($row['score']),
    ]);
}
showtablefooter();

$page = admin_page();
$per_page = admin_per_page();
$count = $userRelation->count();
$mpurl = ADMINSCRIPT . '?' . admin_query_without_page();
$multipage = multi($count, $per_page, $page, $mpurl);
echo $multipage;
