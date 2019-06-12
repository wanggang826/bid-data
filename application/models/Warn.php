<?php
/**
 * User: wgg
 * Date: 19-4-14
 * Time: 下午3:52
 */
class WarnModel
{
    private static $table = 'warning';

    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function getWarnInfo()
    {
        $date = date('Y-m-d');
        $data = Db::Table(self::$table)
            ->Col(['warn_time,warn_msg'])
            ->Where("date > '{$date}'")
            ->OrderBy('id desc')
            ->Get();
        $data = $data ? :['warn_time'=>'','warn_msg'=>''];
        return $data;
    }
}