<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Models\UserData;
use Ganlv\UserFingerprint\Models\UserRelation;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/function/function_core.php';
require_once __DIR__ . '/function/function_index.php';
require_once __DIR__ . '/Models/UserData.php';
require_once __DIR__ . '/Models/UserRelation.php';

function calcRelationAndInsert($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2)
{
    $userDataTable = new UserData();
    $user = $userDataTable->findUser($uid, $sid, $ua_md5, $fingerprint, $fingerprint2, $ip);
    if ($user) {
        $userDataTable->touchById($user['id']);
        return build_response(_('User record exists. Last online time updated.'));
    } else {
        $relatedUsers = $userDataTable->findRelatedUser($uid, $sid, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2);
        $userRelation = new UserRelation();
        foreach ($relatedUsers as $relatedUser) {
            $score = $userRelation->calcRelation([
                'sid' => $sid,
                'ip' => $ip,
                'ip2' => $ip2,
                'fingerprint' => $fingerprint,
                'fingerprint2' => $fingerprint2,
                'ua_md5' => $ua_md5,
            ], $relatedUser);
            if ($score > 1) {
                $userRelation->insertOrUpdateData($uid, $username, $relatedUser['uid'], $relatedUser['username'], $score,
                    serialize([
                        'uid' => $uid,
                        'username' => $username,
                        'sid' => $sid,
                        'ua' => $ua,
                        'ua_md5' => $ua_md5,
                        'fingerprint' => $fingerprint,
                        'fingerprint2' => $fingerprint2,
                        'ip' => $ip,
                        'ip2' => $ip2,
                        'hit' => 1,
                        'created_at' => TIMESTAMP,
                        'last_online_time' => TIMESTAMP,
                    ]),
                    serialize($relatedUser));
            }
        }
        $userDataTable->insertData($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2);
        return build_response(_('User record inserted.'));
    }
}

function main()
{
    if (!request_referer()) {
        return build_response(_('Empty referer.'), 1);
    }

    if (!request_is_referer_valid()) {
        return build_response(_('Invalid referer.'), 2);
    }

    $uid = request_uid();
    if ($uid <= 0) {
        return build_response(_('User not login.'), 3);
    }

    $sid = request_sid();
    if (!$sid) {
        return build_response(_('No valid session id exists.'), 4);
    }

    $fingerprint = request_fingerprint();
    if (!$fingerprint) {
        return build_response(_('Invalid fingerprint.'), 5);
    }

    $fingerprint2 = request_fingerprint2();
    if (!$fingerprint2) {
        return build_response(_('Invalid fingerprint.'), 6);
    }

    $username = request_username();
    $ua = request_ua();
    if (!$ua) {
        return build_response(_('Invalid user agent.'), 7);
    }
    $ua_md5 = md5($ua);
    $ip = request_ip();
    $ip2 = $ip & 0xffffff00;

    $response = calcRelationAndInsert($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2);

    $max_log_count = (int)config('max_log_count');
    $delete_ratio = (float)config('delete_ratio');
    $userDataTable = new UserData();
    $userDataTable->deleteRotated($max_log_count, $delete_ratio);

    return $response;
}

main();
