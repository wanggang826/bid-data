<?php

/**
 * Class Db_Mysqli
 */
class Db_Mysqli extends Db_Db
{

    /**
     * 初始化数据库连接类
     * @param Yaf_Config_Ini $config
     * @param string $alias
     * @return Db_Mysqli
     */
    public static function Init(Yaf_Config_Ini $config, string $alias)
    {
        //存储配置
        if (!isset(self::$config[$alias])) {
            self::$config[$alias] = $config;
        }

        //设置当前
        self::$dbCurrent = $alias;

        if (!self::$instance) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @param Yaf_Config_Ini $config
     * @return \mysqli
     */
    private function connectInit(Yaf_Config_Ini $config)
    {
        //建立连接
        @$Conn = new \mysqli(
            $config->host,
            $config->userName,
            $config->password,
            $config->dbName,
            $config->port
        );
        //捕捉错误
        if ($Conn->connect_errno) {
            exit($Conn->connect_error);
        }
        //初始化字符集
        $Conn->query("set names '" . self::$config[self::$dbCurrent]->charset . "'");
        return $Conn;
    }

    /**
     * 连接数据库
     * @return \mysqli
     */
    protected function getConnection()
    {
        if (!isset(self::$connPool[self::$dbCurrent])) {
            self::$connPool[self::$dbCurrent] = $this->connectInit(self::$config[self::$dbCurrent]);
        }

        return self::$connPool[self::$dbCurrent];
    }

    /**
     * 查询
     * @param string $sql
     * @return array|bool
     */
    public function query(string $sql)
    {
        $conn = $this->getConnection();
        $result = $conn->query($sql);
        if ($result) {
            $return = [];
            $field  = $this->_parseFieldType($result);
            while($row = $result->fetch_assoc())
            {
                foreach ($row as $key=>&$val)
                {
                    settype($val, $field[$key]);
                }
                $return[] = $row;
            }
            return $return;
        } else {
            return false;
        }

    }

    /**
     * execute 写入或更新
     * @param string $sql
     * @return array
     */
    public function execute(string $sql)
    {
        $conn = $this->getConnection();
        return [
            'result'       => $conn->query($sql),
            'affectedRows' => $conn->affected_rows,
            'insertId'     => $conn->insert_id,
        ];
    }

    /**
     * Begin 开始事务
     */
    public function Begin()
    {
        $conn = $this->getConnection();
        $conn->autocommit(false);
        $conn->begin_transaction();
    }

    /**
     * Commit 提交事务
     */
    public function Commit()
    {
        $conn = $this->getConnection();
        $conn->commit();
        $conn->autocommit(true);
    }

    /**
     * Rollback 事务回滚
     */
    public function Rollback()
    {
        $conn = $this->getConnection();
        $conn->rollback();
        $conn->autocommit(true);
    }

    /**
     * Autocommit 是否自动提交
     * @param bool $flag
     */
    public function Autocommit(bool $flag)
    {
        $this->getConnection()->autocommit($flag);
    }

    /**
     * @param mysqli_result $result
     * @return array
     */
    private function _parseFieldType(mysqli_result $result): array
    {
        $fields = [];
        while ($info = $result->fetch_field()) {
            switch ($info->type) {
                case MYSQLI_TYPE_BIT:
                case MYSQLI_TYPE_TINY:
                case MYSQLI_TYPE_SHORT:
                case MYSQLI_TYPE_LONG:
                case MYSQLI_TYPE_LONGLONG:
                case MYSQLI_TYPE_INT24:
                    $type = 'int';
                    break;
                case MYSQLI_TYPE_FLOAT:
                case MYSQLI_TYPE_DOUBLE:
                case MYSQLI_TYPE_DECIMAL:
                case MYSQLI_TYPE_NEWDECIMAL:
                    $type = 'float';
                    break;
                default:
                    $type = 'string';
            }
            $fields[$info->name] = $type;
        }
        return $fields;
    }

}
