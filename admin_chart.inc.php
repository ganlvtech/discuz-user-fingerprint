<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserFingerprint;

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once __DIR__ . '/Models/UserFingerprint.php';
require_once __DIR__ . '/function/function_main.php';

$table = new UserFingerprint;

$categories = [
    0 => ['name' => '1' . _(' Relation Account')],
    1 => ['name' => '2' . _(' Relation Account')],
    2 => ['name' => '3' . _(' Relation Account')],
    3 => ['name' => _('Fingerprint')],
    4 => ['name' => _('Session')],
];
$nodes = [];
$links = [];
$index = 0;
$all_uid_index = [];
$all_fingerprint_index = [];
$all_sid_index = [];


$uid = (int)$_GET['uid'];
if ($uid) {
    $uid_to_search = [$uid];
} else {
    $records = $table->findMultiAccountUidArray();
    foreach ($records as $row) {
        $uid_to_search[] = (int)$row['uid'];
    }
}

$records = $table->findUserByUid($uid_to_search);
$uid_to_search_2 = [];
foreach ($records as $row) {
    $row_uid = (int)$row['uid'];
    if (!isset($all_uid_index[$row_uid])) {
        $nodes[$index] = [
            'name' => $row['username'],
            'category' => 0,
            'symbolSize' => 40,
        ];
        $all_uid_index[$row_uid] = $index;
        ++$index;
        $uid_to_search_2[] = $row_uid;
    }
}
$uid_to_search = $uid_to_search_2;

for ($level = 1; $level <= 2; ++$level) {
    $uid_to_search_2 = [];
    foreach ($uid_to_search as $uid) {
        $records = $table->findRelatedUser($uid);

        foreach ($records['fingerprint_array'] as $fingerprint) {
            if (!isset($all_fingerprint_index[$fingerprint])) {
                $nodes[$index] = [
                    'name' => $fingerprint,
                    'category' => 3,
                    'symbolSize' => 10,
                ];
                $all_fingerprint_index[$fingerprint] = $index;
                ++$index;
            }
            $links[] = [
                'source' => $all_uid_index[$uid],
                'target' => $all_fingerprint_index[$fingerprint],
            ];
        }

        foreach ($records['sid_array'] as $sid) {
            if (!isset($all_sid_index[$sid])) {
                $nodes[$index] = [
                    'name' => $sid,
                    'category' => 4,
                    'symbolSize' => 10,
                ];
                $all_sid_index[$sid] = $index;
                ++$index;
            }
            $links[] = [
                'source' => $all_uid_index[$uid],
                'target' => $all_sid_index[$sid],
            ];
        }

        foreach ($records['related_users'] as $row) {
            $row_uid = (int)$row['uid'];
            if (!isset($all_uid_index[$row_uid])) {
                $nodes[$index] = [
                    'name' => $row['username'],
                    'category' => $level,
                    'symbolSize' => (4 - $level) * 10,
                ];
                $all_uid_index[$row_uid] = $index;
                ++$index;
                $uid_to_search_2[] = $row_uid;
            }
            $links[] = [
                'source' => $all_uid_index[$uid],
                'target' => $all_uid_index[$row_uid],
            ];
        }
    }
    $uid_to_search = $uid_to_search_2;
}

$data = [
    'title' => [
        'text' => _('User Account Relation Visualization')
    ],
    'categories' => $categories,
    'nodes' => $nodes,
    'links' => $links,
];


echo '<script>window.user_relation_data = ', json_encode($data), ';</script>';

echo <<<'EOD'
<div class="echarts-container"><div id="chart"></div></div>
<script src="source/plugin/user_fingerprint/js/admin.min.js"></script>
EOD;
