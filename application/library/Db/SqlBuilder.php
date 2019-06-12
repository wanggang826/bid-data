<?php

/**
 * Class SqlBuilder sql语句组合、执行类
 */
class Db_SqlBuilder {

    /**
     * @var string
     */
    private $where = "";

    /**
     * @var string
     */
    private $column = "*";

    /**
     * @var string
     */
    private $table = "";

    /**
     * @var string
     */
    private $limit = "";

    /**
     * @var string
     */
    private $orderBy = "";

    /**
     * @var string
     */
    private $groupBy = "";

    /**
     * @var string
     */
    private $having = "";

    /**
     * @var string
     */
    private $join = "";

    /**
     * SqlBuilder constructor.
     * @param null $instance
     */
    public function __construct($instance = null) {
        //todo
    }

    /**
     * @return db_Mysqli
     */
    private function getDb() {
        return db_Db::GetDb();
    }

    /**
     * @param string $where
     * @return $this
     */
    public function Where(string $where) {
        $this->where = ($where != '') ? " where {$where} " : '';
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function Table(string $table) {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $column
     * @return $this
     */
    public function Col(array $column) {
        $this->column = ( $column == "" ) ? "*" : implode(',', $column);
        return $this;
    }

    /**
     * @param int $start
     * @param int $num
     * @return $this
     */
    public function Limit(int $start, int $num) {
        $this->limit = " limit {$num}  offset {$start} ";
        return $this;
    }

    /**
     * @param string $orderBy
     * @return $this
     */
    public function OrderBy(string $orderBy) {
        $this->orderBy = ' order by ' . $orderBy;
        return $this;
    }

    /**
     * @param  string $groupBy
     * @return $this
     */
    public function GroupBy(string $groupBy) {
        $this->groupBy = ($groupBy != "") ? " group by {$groupBy} " : '';
        return $this;
    }

    /**
     * @param string $having
     * @return $this
     */
    public function Having(string $having) {
        $this->having = ($having != "") ? " having {$having} " : '';
        return $this;
    }

    /**
     * @param array $join
     * @return $this
     */
    public function Join(array $join) {
        $this->join = implode(' ', $join);
        return $this;
    }

    /**
     * @return array|bool
     */
    public function Find() {
        $result = $this->getDb()->query($this->parseSelect());
        $prefix = '';
        if ($result === false) {
            $prefix = 'execute error: ';
        }
        Log::Debug(
                "{prefix}{sql}", [
            '{sql}' => $this->parseSelect(),
            '{prefix}' => $prefix
                ], 'sql'
        );
        return $result;
    }

    /**
     * @return array|bool
     */
    public function Get() {
        $result = $this->getDb()->query($this->parseSelect());
        $prefix = '';
        if ($result === false) {
            $prefix = 'execute error: ';
        }
        Log::Debug(
                "{prefix}{sql}", [
            '{sql}' => $this->parseSelect(),
            '{prefix}' => $prefix
                ], 'sql'
        );
        return (is_array($result) && count($result) > 0) ? $result[0] : false;
    }

    /**
     * @param array $data
     * @return array
     */
    public function Insert(array $data) {
        $column = implode(',', array_keys($data));
        $values = "'" . implode("','", $data) . "'";
        return $this->_dumpAndExecSql("insert into {$this->table}({$column}) values({$values})");
    }

    /**
     * @param array $data
     * @return array
     */
    public function Update(array $data) {
        $update = '';
        foreach ($data as $key => $val) {
            if(is_array($val)){
                $update = $update . $key . "=" . $val[0] . ",";
            }else{
                $update = $update . $key . "='" . $val . "',";
            }
            
        }
        $update = substr($update, 0, -1);
        $sql = "update {$this->table} set {$update} {$this->where}";
        return $this->_dumpAndExecSql($sql);
    }

    /**
     * @return array
     */
    public function Delete() {
        return $this->_dumpAndExecSql("delete from {$this->table} {$this->where}");
    }

    /**
     * @return string
     */
    public function parseSelect(): string {
        return trim("select  {$this->column} from {$this->table} {$this->join} {$this->where} {$this->groupBy} {$this->having} {$this->orderBy} {$this->limit}");
    }

    private function _dumpAndExecSql(string $sql) {
        $result = $this->getDb()->execute($sql);
        $prefix = '';
        if ($result['result'] === false) {
            $prefix = 'execute error: ';
        }
        Log::Debug(
                "{prefix}{sql}", [
            '{sql}' => $sql,
            '{prefix}' => $prefix
                ], 'sql'
        );
        return $result;
    }

    /**
     * @brief
     *
     * @author Hu zhangzheng
     * @created 2019/3/1 17:06
     * @param $sql
     * @return array
     */
    public function executeSql($sql){
        $result = $this->getDb()->execute($sql);
        $prefix = '';
        if ($result['result'] === false) {
            $prefix = 'execute error: ';
        }
        Log::Debug(
            "{prefix}{sql}", [
            '{sql}' => $sql,
            '{prefix}' => $prefix
        ], 'sql'
        );
        return $result;
    }
    public function querySql($sql){
        $result = $this->getDb()->query($sql);
        $prefix = '';
        if ($result['result'] === false) {
            $prefix = 'execute error: ';
        }
        Log::Debug(
            "{prefix}{sql}", [
            '{sql}' => $sql,
            '{prefix}' => $prefix
        ], 'sql'
        );
        return $result;
    }
}
