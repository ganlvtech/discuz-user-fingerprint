<?php

namespace Ganlv\UserFingerprint\Models;

use DB;
use discuz_table;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class UserFingerprint extends discuz_table
{
    protected $prefixed_table = '';

    public function __construct()
    {
        $this->_table = 'user_fingerprint_log';
        $this->_pk = 'id';
        $this->prefixed_table = DB::table($this->_table);
        parent::__construct();
    }

    /**
     * sid 个数
     *
     * @return int
     */
    public function countSid()
    {
        return (int)DB::result_first("SELECT COUNT(DISTINCT(`sid`)) AS `count` FROM {$this->prefixed_table}");
    }

    /**
     * fingerprint 个数
     *
     * @return int
     */
    public function countFingerprint()
    {
        return (int)DB::result_first("SELECT COUNT(DISTINCT(`fingerprint`)) AS `count` FROM {$this->prefixed_table}");
    }

    /**
     * uid 个数
     *
     * @return int
     */
    public function countUser()
    {
        return (int)DB::result_first("SELECT COUNT(DISTINCT(`uid`)) AS `count` FROM {$this->prefixed_table}");
    }

    /**
     * 按 sid 对应 fingerprint 个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [sid => ['sid' => sid, 'fingerprint_count' => fingerprint_count, 'hit' => hit], ...]
     */
    public function fetchSidFingerprintCountDesc($start = 0, $limit = 20)
    {
        $result = [];
        $records = DB::fetch_all("SELECT `sid`, COUNT(*) AS `count`, SUM(`hit`) AS `hit` FROM `{$this->prefixed_table}` GROUP BY `sid` ORDER BY `count` DESC LIMIT {$start}, {$limit}");
        foreach ($records as $row) {
            $row['count'] = (int)$row['count'];
            $row['hit'] = (int)$row['hit'];
            $result[$row['sid']] = $row;
        }
        return $result;
    }

    /**
     * 按 fingerprint 对应 sid 个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [fingerprint => ['fingerprint' => fingerprint, 'sid_count' => sid_count, 'hit' => hit], ...]
     */
    public function fetchFingerprintSidCountDesc($start = 0, $limit = 20)
    {
        $result = [];
        $records = DB::fetch_all("SELECT `fingerprint`, COUNT(*) AS `count`, SUM(`hit`) AS `hit` FROM `{$this->prefixed_table}` GROUP BY `fingerprint` ORDER BY `count` DESC LIMIT {$start}, {$limit}");
        foreach ($records as $row) {
            $row['count'] = (int)$row['count'];
            $row['hit'] = (int)$row['hit'];
            $result[$row['fingerprint']] = $row;
        }
        return $result;
    }

    /**
     * 按 uid 对应记录个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [uid => ['uid' => uid, 'username' => username, 'log_count' => log_count, 'hit' => hit], ...]
     */
    public function fetchUserLogCountDesc($start = 0, $limit = 20)
    {
        $result = [];
        $records = DB::fetch_all("SELECT `uid`, `username`, COUNT(*) AS `count`, SUM(`hit`) AS `hit` FROM `{$this->prefixed_table}` GROUP BY `uid` ORDER BY `count` DESC LIMIT {$start}, {$limit}");
        foreach ($records as $row) {
            $row['count'] = (int)$row['count'];
            $row['hit'] = (int)$row['hit'];
            $result[$row['uid']] = $row;
        }
        return $result;
    }

    /**
     * 获取指定 sid 的全部记录
     *
     * @param array $sid_array
     *
     * @return array [sid => [list of records], ...]
     */
    public function fetchAllBySid($sid_array)
    {
        $result = [];
        $in = implode(', ', DB::quote($sid_array));
        $records = DB::fetch_all("SELECT * FROM `{$this->prefixed_table}` WHERE `sid` IN ({$in}) ORDER BY `id` ASC");
        foreach ($records as $row) {
            if (!isset($result[$row['sid']])) {
                $result[$row['sid']] = [];
            }
            $result[$row['sid']][] = $row;
        }
        return $result;
    }

    /**
     * 获取指定 fingerprint 的全部记录
     *
     * @param array $fingerprint_array
     *
     * @return array [fingerprint => [list of records], ...]
     */
    public function fetchAllByFingerprint($fingerprint_array)
    {
        $result = [];
        $in = implode(', ', DB::quote($fingerprint_array));
        $records = DB::fetch_all("SELECT * FROM `{$this->prefixed_table}` WHERE `fingerprint` IN ({$in}) ORDER BY `id` ASC");
        foreach ($records as $row) {
            if (!isset($result[$row['fingerprint']])) {
                $result[$row['fingerprint']] = [];
            }
            $result[$row['fingerprint']][] = $row;
        }
        return $result;
    }

    /**
     * 获取指定 uid 的全部记录
     *
     * @param array $uid_array
     *
     * @return array [uid => [list of records], ...]
     */
    public function fetchAllByUid($uid_array)
    {
        $result = [];
        $in = implode(', ', DB::quote($uid_array));
        $records = DB::fetch_all("SELECT * FROM `{$this->prefixed_table}` WHERE `uid` IN ({$in}) ORDER BY `id` ASC");
        foreach ($records as $row) {
            if (!isset($result[$row['uid']])) {
                $result[$row['uid']] = [];
            }
            $result[$row['uid']][] = $row;
        }
        return $result;
    }

    /**
     * 通过 uid, sid, fingerprint 获取 id, hit
     *
     * @param int $uid
     * @param string $sid
     * @param string $fingerprint
     *
     * @return array
     */
    public function fetchIdHitByUidSidFingerprint($uid, $sid, $fingerprint)
    {
        $uid = DB::quote($uid);
        $sid = DB::quote($sid);
        $fingerprint = DB::quote($fingerprint);
        $result = DB::fetch_first("SELECT `id`, `hit` FROM `{$this->prefixed_table}` WHERE `uid` = {$uid} AND `sid` = {$sid} AND `fingerprint` = {$fingerprint} LIMIT 1");
        if ($result) {
            $result['id'] = (int)$result['id'];
            $result['hit'] = (int)$result['hit'];
        }
        return $result;
    }

    /**
     * 更新一条记录的最后在线时间，访问次数 +1
     *
     * @param int $id
     *
     * @return mixed
     */
    public function touchById($id)
    {
        $id = DB::quote($id);
        $timestamp = DB::quote(TIMESTAMP);
        return DB::query("UPDATE {$this->prefixed_table} SET `last_online_time` = {$timestamp}, `hit` = `hit` + 1 WHERE `id` = {$id} LIMIT 1");
    }

    public function insertData($data)
    {
        return DB::insert($this->_table, [
            'fingerprint' => substr($data['fingerprint'], 0, 32),
            'sid' => substr($data['sid'], 0, 6),
            'uid' => $data['uid'],
            'username' => $data['username'],
            'ip' => ip2long($data['ip']),
            'ua' => substr($data['ua'], 0, 1024),
            'hit' => 1,
            'created_at' => TIMESTAMP,
            'last_online_time' => TIMESTAMP,
        ]);
    }

    /**
     * 滚动删除较早的记录
     *
     * @param int $max_log_count
     * @param float $delete_ratio
     *
     * @return bool|null null: 没有进行操作 | false: 删除失败 | true: 删除成功
     */
    public function deleteRotated($max_log_count = 100000, $delete_ratio = 0.01)
    {
        if ($max_log_count <= 0) {
            return null;
        }
        $table = DB::table($this->_table);
        $log_count = $this->count();
        if ($log_count <= $max_log_count) {
            return null;
        }
        if ($delete_ratio <= 0 || $delete_ratio > 0.5) {
            $delete_ratio = 0.01;
        }
        $delete_log_count = (int)($log_count * $delete_ratio);
        $limit = $log_count - $delete_log_count;
        $delete_id = DB::fetch_first("SELECT `id` FROM `$table` ORDER BY `id` DESC LIMIT $limit, 1");
        $delete_id = (int)$delete_id['id'];
        $delete_id_quoted = DB::quote($delete_id);
        return DB::delete($this->_table, "`id` <= $delete_id_quoted");
    }

    public function findUserByFingerprintOrSid($fingerprint_array, $sid_array)
    {
        if (!$fingerprint_array || !$fingerprint_array) {
            return [];
        }
        $fingerprint_in = implode(', ', DB::quote($fingerprint_array));
        $sid_in = implode(', ', DB::quote($sid_array));
        return DB::fetch_all("SELECT DISTINCT(`uid`), `username` FROM `{$this->prefixed_table}` WHERE `fingerprint` IN ({$fingerprint_in}) OR `sid` IN ({$sid_in})");
    }

    public function findRelatedUser($uid)
    {
        $records = DB::fetch_all("SELECT `fingerprint`, `sid` FROM `{$this->prefixed_table}` WHERE `uid` = {$uid}");
        $fingerprint_array = [];
        $sid_array = [];
        foreach ($records as $row) {
            $fingerprint_array[] = $row['fingerprint'];
            $sid_array[] = $row['sid'];
        }
        $related_users = $this->findUserByFingerprintOrSid($fingerprint_array, $sid_array);
        return [
            'fingerprint_array' => $fingerprint_array,
            'sid_array' => $sid_array,
            'related_users' => $related_users,
        ];
    }

    public function findMultiAccountUidArray($start = 0, $limit = 20)
    {
        $fingerprint_array = [];
        $sid_array = [];
        $records = DB::fetch_all("SELECT `fingerprint`, COUNT(DISTINCT(`uid`)) AS `count` FROM `pre_user_fingerprint_log` GROUP BY `fingerprint` ORDER BY `count` DESC LIMIT {$start}, {$limit}");;
        foreach ($records as $row) {
            if ((int)$row['count'] > 1) {
                $fingerprint_array[] = $row['fingerprint'];
            }
        }
        $records = DB::fetch_all("SELECT `sid`, COUNT(DISTINCT(`uid`)) AS `count` FROM `pre_user_fingerprint_log` GROUP BY `sid` ORDER BY `count` DESC LIMIT {$start}, {$limit}");
        foreach ($records as $row) {
            if ((int)$row['count'] > 1) {
                $sid_array[] = $row['sid'];
            }
        }
        return $this->findUserByFingerprintOrSid($fingerprint_array, $sid_array);
    }

    public function findUserByUid($uid_array)
    {
        if (!$uid_array) {
            return [];
        }
        $in = implode(', ', DB::quote($uid_array));
        return DB::fetch_all("SELECT DISTINCT(`uid`), `username` FROM `{$this->prefixed_table}` WHERE `uid` IN ({$in})");
    }
}
