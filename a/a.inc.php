<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$_GET['fingerprint'] = $_GET['a'];
$_GET['fingerprint2'] = $_GET['b'];

include __DIR__ . '/../user_fingerprint/user_fingerprint.inc.php';
