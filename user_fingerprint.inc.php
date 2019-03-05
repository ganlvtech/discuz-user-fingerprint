<?php

namespace Ganlv\UserFingerprint;

use Ganlv\UserFingerprint\Libraries\Request;
use Ganlv\UserFingerprint\Models\UserData;
use Ganlv\UserFingerprint\Models\UserRelation;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once __DIR__ . '/Libraries/Request.php';
require_once __DIR__ . '/Models/UserData.php';
require_once __DIR__ . '/Models/UserRelation.php';

function _($str)
{
    return lang('plugin/user_fingerprint', $str);
}

function build_response($msg = 'OK', $code = 0, $data = null)
{
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ];
}

function convert_to_utf_8($data)
{
    if (CHARSET !== 'utf-8') {
        if (is_string($data)) {
            $data = iconv(CHARSET, 'utf-8', $data);
        } elseif (is_array($data)) {
            foreach ($data as &$item) {
                $item = convert_to_utf_8($item);
            }
        }
    }
    return $data;
}

function json_encode_with_charset($data)
{
    return json_encode(convert_to_utf_8($data));
}

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
                $userRelation->insertData($uid, $username, $relatedUser['uid'], $relatedUser['username'], $score, json_encode([
                    'user1' => [
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
                    ],
                    'user2' => $relatedUser,
                ]));
            }
        }
        $userDataTable->insertData($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2);
        return build_response(_('User record inserted.'));
    }
}

function main()
{
    if (!Request::referer()) {
        return build_response(_('Empty referer.'), 1);
    }

    if (!Request::isRefererValid()) {
        return build_response(_('Invalid referer.'), 2);
    }

    $uid = Request::uid();
    if ($uid <= 0) {
        return build_response(_('User not login.'), 3);
    }

    $sid = Request::sid();
    if (!$sid) {
        return build_response(_('No valid session id exists.'), 4);
    }

    $fingerprint = Request::fingerprint();
    if (!$fingerprint) {
        return build_response(_('Invalid fingerprint.'), 5);
    }

    $fingerprint2 = Request::fingerprint2();
    if (!$fingerprint2) {
        return build_response(_('Invalid fingerprint.'), 6);
    }

    $username = Request::username();
    $ua = Request::ua();
    if (!$ua) {
        return build_response(_('Invalid user agent.'), 7);
    }
    $ua_md5 = md5($ua);
    $ip = Request::ip();
    $ip2 = $ip & Request::IP_MASK;

    $response = calcRelationAndInsert($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2);

    $max_log_count = (int)Request::config('max_log_count');
    $delete_ratio = (float)Request::config('delete_ratio');
    $userDataTable = new UserData();
    $userDataTable->deleteRotated($max_log_count, $delete_ratio);

    return $response;
}

$result = main();
header('Content-Type: text/javascript; charset=' . CHARSET);
echo 'console.log(', json_encode_with_charset($result), ');';
