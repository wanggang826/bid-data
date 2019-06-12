<?php

/**
 * Class Db
 */
class Db
{
    private static $prefix = 'Db_';

    /**
     * 获取数据库连接对象
     * @param string $name
     * @return bool|Db_Mysqli
     */
    public static function Get(string $name='master')
    {
        $config = Yaf_Registry::get(CFG)->db->get($name);
        if(is_null($config))
        {
            return false;
        }
        $class  = static::$prefix . ucfirst($config->driver);
        return $class::Init($config, $name);
    }
    
    /**
     * Table 启动sql组合
     * @param string $table
     * @param string $dbAlias
     * @return db_SqlBuilder
     */
    public static function Table(string $table, string $dbAlias='master')
    {
        Db::Get($dbAlias);
        $sqlBuilder = new Db_SqlBuilder();
        return $sqlBuilder->Table($table);
    }

    public static function ParseArray2Where($condition) : string
    {
        if (empty($condition)) {
            return $condition;
        }
        if (!is_array($condition)) {
            return $condition;
        }
        $where = '';
        foreach ($condition as $key=>$value)
        {
            if(empty($where))
            {
                $where .= " {$key}='{$value}' ";
                continue;
            }
            $where .= " and {$key}='{$value}' ";
        }
        return $where;
    }
}