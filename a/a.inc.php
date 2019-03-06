<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$_GET['fingerprint'] = $_GET['a'];
$_GET['fingerprint2'] = $_GET['b'];

include libfile('user_fingerprint.inc', 'plugin/user_fingerprint');

