<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserFingerprint;

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once __DIR__ . '/function_main.php';
require_once __DIR__ . '/function_admin.php';

function admin_render($type)
{
    admin_show_stylesheet();
    $table = new UserFingerprint;
    switch ($type) {
        case 'fingerprint':
            showtableheader(_('Fingerprint Session Count Top'));
            $counts = $table->fetchFingerprintSidCountDesc(admin_query_start());
            $records = $table->fetchAllByFingerprint(array_keys($counts));
            $count = $table->countFingerprint();
            break;
        case 'user':
            showtableheader(_('User Log Count Top'));
            $counts = $table->fetchUserLogCountDesc(admin_query_start());
            $records = $table->fetchAllByUid(array_keys($counts));
            $count = $table->countSid();
            break;
        case 'sid':
            showtableheader(_('Session Fingerprint Count Top'));
            $counts = $table->fetchSidFingerprintCountDesc(admin_query_start());
            $records = $table->fetchAllBySid(array_keys($counts));
            $count = $table->countUser();
            break;
        default:
            return;
    }

    admin_show_table_subtitle();
    foreach ($counts as $key => $item) {
        admin_show_table_row_title($item);
        foreach ($records[$key] as $row) {
            admin_show_table_row_content($row, $item);
        }
    }
    showtablefooter();
    admin_show_paginator($count);
}
