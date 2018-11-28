<?php

namespace Ganlv\UserFingerprint;

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once __DIR__ . '/function/function_admin_template.php';

admin_render('user');
