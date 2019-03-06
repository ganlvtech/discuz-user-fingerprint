<?php

namespace Ganlv\UserFingerprint\Models;

use DB;
use discuz_table;

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class UserData extends discuz_table
{
    protected $prefixed_table = '';

    public function __construct()
    {
        $this->_table = 'user_fingerprint_data';
        $this->_pk = 'id';
        $this->prefixed_table = DB::table($this->_table);
        parent::__construct();
    }

    /**
     * 找出相同用户
     *
     * @param int $uid
     * @param string $sid
     * @param string $ua_md5
     * @param string $fingerprint
     * @param string $fingerprint2
     * @param int $ip
     *
     * @return array
     */
    public function findUser($uid, $sid, $ua_md5, $fingerprint, $fingerprint2, $ip)
    {
        $record = DB::fetch_first("SELECT * FROM `{$this->prefixed_table}` WHERE  `uid` = '$uid' AND `sid` = '$sid' AND `ua_md5` = '$ua_md5' AND `fingerprint` = '$fingerprint' AND `fingerprint2` = '$fingerprint2' AND `ip` = '$ip'");
        if ($record) {
            $record['id'] = (int)$record['id'];
            $record['uid'] = (int)$record['uid'];
            $record['ip'] = (int)$record['ip'];
            $record['ip2'] = (int)$record['ip2'];
            $record['hit'] = (int)$record['hit'];
            $record['created_at'] = (int)$record['created_at'];
            $record['last_online_time'] = (int)$record['last_online_time'];
        }
        return $record;
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

    /**
     * 找出相关数据
     *
     * @param int $exclude_uid
     * @param string $sid
     * @param string $ua_md5
     * @param string $fingerprint
     * @param string $fingerprint2
     * @param int $ip
     * @param int $ip2
     *
     * @return array
     */
    public function findRelatedUser($exclude_uid, $sid, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2)
    {
        $records = DB::fetch_all("SELECT * FROM `{$this->prefixed_table}` WHERE (`sid` = '$sid' OR `ua_md5` = '$ua_md5' OR `fingerprint` = '$fingerprint' OR `fingerprint2` = '$fingerprint2' OR `ip` = '$ip' OR `ip2` = '$ip2') AND `uid` <> '$exclude_uid'");
        foreach ($records as &$record) {
            $record['id'] = (int)$record['id'];
            $record['uid'] = (int)$record['uid'];
            $record['ip'] = (int)$record['ip'];
            $record['ip2'] = (int)$record['ip2'];
            $record['hit'] = (int)$record['hit'];
            $record['created_at'] = (int)$record['created_at'];
            $record['last_online_time'] = (int)$record['last_online_time'];
        }
        return $records;
    }

    /**
     * 插入数据
     *
     * @param int $uid
     * @param string $username
     * @param string $sid
     * @param string $ua
     * @param string $ua_md5
     * @param string $fingerprint
     * @param string $fingerprint2
     * @param int $ip
     * @param int $ip2
     *
     * @return mixed
     */
    public function insertData($uid, $username, $sid, $ua, $ua_md5, $fingerprint, $fingerprint2, $ip, $ip2)
    {
        return DB::insert($this->_table, [
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
}
