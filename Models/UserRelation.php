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
        $score = 0;

        if (!empty($userData1['sid']) && !empty($userData2['sid']) && $userData1['sid'] === $userData2['sid']) {
            $score += 9;
        }

        if (!empty($userData1['ip']) && !empty($userData2['ip']) && $userData1['ip'] === $userData2['ip']) {
            $score += 6;
        } elseif (!empty($userData1['ip2']) && !empty($userData2['ip2']) && $userData1['ip2'] === $userData2['ip2']) {
            $score += 3;
        }

        if (!empty($userData1['fingerprint']) && !empty($userData2['fingerprint']) && $userData1['fingerprint'] === $userData2['fingerprint']) {
            $score += 3;
        } elseif (!empty($userData1['fingerprint2']) && !empty($userData2['fingerprint2']) && $userData1['fingerprint2'] === $userData2['fingerprint2']) {
            $score += 2;
        } elseif (!empty($userData1['ua_md5']) && !empty($userData2['ua_md5']) && $userData1['ua_md5'] === $userData2['ua_md5']) {
            $score += 1;
        }

        return $score;
    }

    /**
     * 插入一条数据
     *
     * @param int $uid1
     * @param string $username1
     * @param int $uid2
     * @param string $username2
     * @param int $score
     * @param array $data1
     * @param array $data2
     *
     * @return mixed
     */
    public function insertOrUpdateData($uid1, $username1, $uid2, $username2, $score, $data1, $data2)
    {
        if ($uid1 > $uid2) {
            $tmp = $uid1;
            $uid1 = $uid2;
            $uid2 = $tmp;
            $tmp = $username1;
            $username1 = $username2;
            $username2 = $tmp;
            $tmp = $data1;
            $data1 = $data2;
            $data2 = $tmp;
        } elseif ($uid1 === $uid2) {
            return null;
        }
        $record = $this->findByUid($uid1, $uid2);
        if ($record) {
            if ($score >= (int)$record['score']) {
                $id = DB::quote($record['id']);
                return DB::update($this->_table, [
                    'uid1' => $uid1,
                    'username1' => $username1,
                    'uid2' => $uid2,
                    'username2' => $username2,
                    'score' => $score,
                    'data1' => json_encode($data1),
                    'data2' => json_encode($data2),
                ], "`id` = {$id}");
            } else {
                return 0;
            }
        } else {
            return DB::insert($this->_table, [
                'uid1' => $uid1,
                'username1' => $username1,
                'uid2' => $uid2,
                'username2' => $username2,
                'score' => $score,
                'data1' => json_encode($data1),
                'data2' => json_encode($data2),
            ]);
        }
    }

    /**
     * @param int $uid1
     * @param int $uid2
     *
     * @return array
     */
    public function findByUid($uid1, $uid2)
    {
        $uid1 = DB::quote($uid1);
        $uid2 = DB::quote($uid2);
        return DB::fetch_first("SELECT * FROM `{$this->prefixed_table}` WHERE `uid1` = {$uid1} AND `uid2` = {$uid2}");
    }

    /**
     * @param int $page
     * @param int $perpage
     *
     * @return array
     */
    public function fetchAll($page, $perpage = 20)
    {
        $start = ($page - 1) * $perpage;
        $records = DB::fetch_all("SELECT * FROM `{$this->prefixed_table}` ORDER BY `score` DESC LIMIT {$start}, {$perpage}");
        foreach ($records as &$record) {
            $record['id'] = (int)$record['id'];
            $record['uid1'] = (int)$record['uid1'];
            $record['uid2'] = (int)$record['uid2'];
            $record['data1'] = json_decode($record['data1'], true);
            $record['data2'] = json_decode($record['data2'], true);
        }
        return $records;
    }
}
