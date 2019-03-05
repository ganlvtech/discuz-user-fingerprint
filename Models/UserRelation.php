<?php

namespace Ganlv\UserFingerprint\Models;

use DB;
use discuz_table;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class UserRelation extends discuz_table
{
    protected $prefixed_table = '';

    public function __construct()
    {
        $this->_table = 'user_fingerprint_relation';
        $this->_pk = 'id';
        $this->prefixed_table = DB::table($this->_table);
        parent::__construct();
    }

    /**
     * 计算两个用户的关系，必须包含字段 sid, ip, ip2, fingerprint, fingerprint2, ua_md5
     *
     * @param $userData1
     * @param $userData2
     *
     * @return float|int
     */
    public function calcRelation($userData1, $userData2)
    {
        if (!empty($userData1['sid']) && !empty($userData2['sid']) && $userData1['sid'] === $userData2['sid']) {
            $score1 = 3;
        } elseif (!empty($userData1['ip']) && !empty($userData2['ip']) && $userData1['ip'] === $userData2['ip']) {
            $score1 = 2;
        } elseif (!empty($userData1['ip2']) && !empty($userData2['ip2']) && $userData1['ip2'] === $userData2['ip2']) {
            $score1 = 1;
        } else {
            $score1 = 0;
        }

        if (!empty($userData1['fingerprint']) && !empty($userData2['fingerprint']) && $userData1['fingerprint'] === $userData2['fingerprint']) {
            $score2 = 3;
        } elseif (!empty($userData1['fingerprint2']) && !empty($userData2['fingerprint2']) && $userData1['fingerprint2'] === $userData2['fingerprint2']) {
            $score2 = 2;
        } elseif (!empty($userData1['ua_md5']) && !empty($userData2['ua_md5']) && $userData1['ua_md5'] === $userData2['ua_md5']) {
            $score2 = 1;
        } else {
            $score2 = 0;
        }

        return $score1 * 3 + $score2;
    }

    /**
     * 插入一条数据
     *
     * @param int $uid1
     * @param string $username1
     * @param int $uid2
     * @param string $username2
     * @param int $score
     * @param string $data
     *
     * @return mixed
     */
    public function insertData($uid1, $username1, $uid2, $username2, $score, $data)
    {
        if ($uid1 > $uid2) {
            $tmp = $uid1;
            $uid1 = $uid2;
            $uid2 = $tmp;
            $tmp = $username1;
            $username1 = $username2;
            $username2 = $tmp;
        } elseif ($uid1 === $uid2) {
            return null;
        }
        return DB::insert($this->_table, [
            'uid1' => $uid1,
            'username1' => $username1,
            'uid2' => $uid2,
            'username2' => $username2,
            'score' => $score,
            'data' => $data,
        ]);
    }
}
