<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_user_fingerprint extends discuz_table
{
    public function __construct()
    {
        $this->_table = 'user_fingerprint_log';
        $this->_pk = 'id';
        parent::__construct();
    }

    /**
     * 按 sid 对应 fingerprint 个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [sid => ['sid' => sid, 'fingerprint_count' => fingerprint_count, 'hit' => hit], ...]
     */
    public function fetch_sid_fingerprint_count_desc($start = 0, $limit = 0)
    {
        $result = [];
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT `sid`, COUNT(*) AS `fingerprint_count`, SUM(`hit`) AS `hit` FROM `$table` GROUP BY `sid` ORDER BY `fingerprint_count` DESC LIMIT $start, $limit");
        foreach ($records as $row) {
            $result[$row['sid']] = [
                'sid' => $row['sid'],
                'fingerprint_count' => (int)$row['fingerprint_count'],
                'hit' => (int)$row['hit'],
            ];
        }
        return $result;
    }

    /**
     * sid 个数
     *
     * @return int
     */
    public function fetch_sid_count()
    {
        $table = DB::table($this->_table);
        $result = DB::result_first("SELECT COUNT(DISTINCT(`sid`)) AS `count` FROM $table");
        return (int)$result;
    }

    /**
     * 按 fingerprint 对应 sid 个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [fingerprint => ['fingerprint' => fingerprint, 'sid_count' => sid_count, 'hit' => hit], ...]
     */
    public function fetch_fingerprint_sid_count_desc($start = 0, $limit = 20)
    {
        $result = [];
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT `fingerprint`, COUNT(*) AS `sid_count`, SUM(`hit`) AS `hit` FROM `$table` GROUP BY `fingerprint` ORDER BY `sid_count` DESC LIMIT $start, $limit");
        foreach ($records as $row) {
            $result[$row['fingerprint']] = [
                'fingerprint' => $row['fingerprint'],
                'sid_count' => (int)$row['sid_count'],
                'hit' => (int)$row['hit'],
            ];
        }
        return $result;
    }

    /**
     * fingerprint 个数
     *
     * @return int
     */
    public function fetch_fingerprint_count()
    {
        $table = DB::table($this->_table);
        $result = DB::result_first("SELECT COUNT(DISTINCT(`fingerprint`)) AS `count` FROM $table");
        return (int)$result;
    }

    /**
     * 按 uid 对应记录个数从多到少排序
     *
     * @param int $start
     * @param int $limit
     *
     * @return array [uid => ['uid' => uid, 'username' => username, 'log_count' => log_count, 'hit' => hit], ...]
     */
    public function fetch_user_log_count_desc($start = 0, $limit = 20)
    {
        $result = [];
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT `uid`, `username`, COUNT(*) AS `log_count`, SUM(`hit`) AS `hit` FROM `$table` GROUP BY `uid` ORDER BY `log_count` DESC LIMIT $start, $limit");
        foreach ($records as $row) {
            $result[$row['uid']] = [
                'uid' => $row['uid'],
                'username' => $row['username'],
                'log_count' => (int)$row['log_count'],
                'hit' => (int)$row['hit'],
            ];
        }
        return $result;
    }

    /**
     * 用户个数
     *
     * @return int
     */
    public function fetch_user_count()
    {
        $table = DB::table($this->_table);
        $result = DB::result_first("SELECT COUNT(DISTINCT(`uid`)) AS `count` FROM $table");
        return (int)$result;
    }

    /**
     * 获取指定 sid 的全部记录
     *
     * @param array $sid_array
     *
     * @return array [sid => [list of records], ...]
     */
    public function fetch_all_by_sid($sid_array)
    {
        $result = [];
        if (!$sid_array) {
            return $result;
        }
        $sid_array = DB::quote($sid_array);
        $sid_imploded = implode(', ', $sid_array);
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT * FROM `$table` WHERE `sid` IN ($sid_imploded) ORDER BY `id` ASC");
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
    public function fetch_all_by_fingerprint($fingerprint_array)
    {
        $result = [];
        if (!$fingerprint_array) {
            return $result;
        }
        $fingerprint_array = DB::quote($fingerprint_array);
        $fingerprint_imploded = implode(', ', $fingerprint_array);
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT * FROM `$table` WHERE `fingerprint` IN ($fingerprint_imploded) ORDER BY `id` ASC");
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
    public function fetch_all_by_uid($uid_array)
    {
        $result = [];
        if (!$uid_array) {
            return $result;
        }
        $uid_array = DB::quote($uid_array);
        $uid_imploded = implode(', ', $uid_array);
        $table = DB::table($this->_table);
        $records = DB::fetch_all("SELECT * FROM `$table` WHERE `uid` IN ($uid_imploded) ORDER BY `id` ASC");
        foreach ($records as $row) {
            if (!isset($result[$row['uid']])) {
                $result[$row['uid']] = [];
            }
            $result[$row['uid']][] = $row;
        }
        return $result;
    }

    /**
     * 滚动删除较早的记录
     *
     * @param int $max_log_count
     * @param float $delete_ratio
     *
     * @return bool|null null: 没有进行操作 | false: 删除失败 | true: 删除成功
     */
    public function delete_rotated($max_log_count = 100000, $delete_ratio = 0.01)
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
        return DB::delete(USER_FINGERPRINT_LOG_TABLE_NAME, "`id` <= $delete_id_quoted");
    }
}
