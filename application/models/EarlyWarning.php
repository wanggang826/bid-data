<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:54
 */
class EarlyWarningModel
{
    private static $table = 'early_warning';

    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function Detail($where)
    {
        return Db::Table(static::$table)->Where($where)->Get();
    }

    public static function Update($id, $data)
    {
        return Db::Table(static::$table)->Where("id={$id}")->Update($data)['result'];
    }
    
}
