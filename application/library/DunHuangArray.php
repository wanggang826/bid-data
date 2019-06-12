<?php
/**
 * 一个超级强大的数组的工具类
 * @author  panxiongfei
 * @date    2018-12-26 10:14
 */
class DunHuangArray
{

    /**
     * 剔除空数组元素
     * @param array $arr
     * @param string $trim 是否进行trim
     */
    public static function removeEmpty(&$arr, $trim = true)
    {
        if (!is_array($arr))
            return false;
        foreach ($arr as $k => &$v)
        {
            if (is_array($v))
            {
                self::removeEmpty($v, $trim);
            }
            else if (is_string($v))
            {
                $v = trim($v);
                if ($v == "")
                {
                    unset($arr[$k]);
                }
                else
                {
                    $arr[$k] = $v;
                }
            }
        }
    }

    /**
     * 对数组元素依次trim
     * @param array $arr
     * @param boolean $recursive
     * @return boolean
     */
    public static function trim(&$arr, $recursive = true)
    {
        if (!is_array($arr))
            return false;
        foreach ($arr as $k => &$v)
        {
            if (is_array($v))
            {
                self::trim($v, $recursive);
            }
            else if (is_string($v))
            {
                $v = trim($v);
            }
        }
        return true;
    }

    /**
     * 获取一个多维数组的指定的列
     * @example
     * <pre>
     * $arr=array(
     *    array('id'=>1,'name'=>"aaa"),
     *    array('id'=>2,'name'=>"bbb"),
     *    array('id'=>3,'name'=>"ccc"),
     *    )
     * getCols($arr,'id');
     * 得到结果：
     * array(1,2,3)
     * </pre>
     * @param array $arr
     * @param String $colName
     * @param boolean $unique 结果是否去重
     * @return array
     */
    public static function getCols($arr, $colName, $unique = false)
    {
        if (!is_array($arr))
            return array();
        $result = array();
        foreach ($arr as $one)
        {
            $tmp = self::getRow($one, $colName);
            if (null != $tmp)
            {
                $result[] = $tmp;
            }
        }
        if ($unique)
            $result = self::unique($result);
        return $result;
    }

    /**
     * 支持二维数组的unique
     * @param array $arr
     * @return array
     */
    public static function unique($arr)
    {
        if (!is_array($arr))
            return array();
        return array_map("unserialize", array_unique(array_map("serialize", $arr)));
    }

    /**
     * 获取多维数组的一个key的统计数据
     * @param array $arr
     * @param string $colName
     * @param string $type 类型：max、min、sum、avg
     * @return NULL|mixed|number
     */
    public static function getCol($arr, $colName, $type)
    {
        if (!is_array($arr) || empty($arr))
            return null;
        $type = strtolower($type);
        $colsVals = self::getCols($arr, $colName);
        if ("max" == $type)
        {
            return max($colsVals);
        }
        else if ('min' == $type)
        {
            return min($colsVals);
        }
        else if ('sum' == $type)
        {
            return array_sum($colsVals);
        }
        else if ('avg' == $type)
        {
            $c = count($colsVals);
            return $c == 0 ? 0 : array_sum($colsVals) / $c;
        }
        return null;
    }

    /**
     * 获取多维数组的一行/项数据
     * @param array $arr
     * @param string $rowName 如“0.name”
     * @param string $type 结果类型 所有的settype方法支持的类型
     * @return mixed
     * @example
     * <pre>
     *       $arr=array(
      array('id'=>1,'name'=>"aaa"),
      array('id'=>array('a','b'),'name'=>"ccc"),
      );
      $result=self::getRow($arr,"1.id.0");
      得到结果为'a'
     * </pre>
     */
    public static function getRow($arr, $rowName, $type = null)
    {
        settype($arr, 'array');
        $row_arr = self::nameSplit($rowName);
        $data = $arr;
        foreach ($row_arr as $name)
        {
            $data = is_array($data) && isset($data[$name]) ? $data[$name] : null;
        }
        if ($type)
            settype($data, $type);
        return $data;
    }

    /**
     * 将一个字符串按照分隔符进行切分，若分隔符前面有\则不切分
     * @param string $nameStr 待分割的字符
     * @param string $delimiter 分隔符，单个字符 如.,
     * @return array
     */
    public static function nameSplit($nameStr, $delimiter = ".")
    {
        $names = preg_split("/(?<!\\\)\\{$delimiter}/", $nameStr);
        foreach ($names as $i => $name)
        {
            $names[$i] = str_replace("\\{$delimiter}", $delimiter, $name);
        }
        return $names;
    }

    /**
     * 将一个二维数组 按照指定的$k作为主键
     * 若$vk不为空则其值为$vk的值
     * @example
     * <pre>
     * $arr=array(
     *    array('id'=>1,'name'="aaa"),
     *    array('id'=>2,'name'="bbb"),
     *    array('id'=>3,'name'="ccc"),
     *    )
     * self::toHash($arr,'id');
     *  结果为：
     *  $arr=array(
     *    1=>array('id'=>1,'name'="aaa"),
     *    2=>array('id'=>2,'name'="bbb"),
     *    3=>array('id'=>3,'name'="ccc"),
     *    )
     *  self::toHash($arr,'id','name');
     *    结果为：
     *  $arr=array(
     *    1=>"aaa",
     *    2=>"bbb",
     *    3=>"ccc",
     *    )
     *  </pre>
     * @param array $arr
     * @param string $k key字段
     * @param string $vk 可选的值的字段，若不为空则值为该字段的值
     * @param bool 是否多组记录
     * @return array
     */
    public static function toHash($arr, $k, $vk = null, $is_multi = false)
    {
        if (!is_array($arr))
            return array();
        $result = array();
        foreach ($arr as $one)
        {
            if (isset($one[$k]))
            {
                $value = empty($vk) ? $one : (isset($one[$vk]) ? trim($one[$vk]) : "");
                if ($is_multi){
                    $result[trim($one[$k])][] = $value ;
                }else{
                    $result[trim($one[$k])] = $value ;
                }
            }
        }
        return $result;
    }

    /**
     * 将hashMap转换为普通数组
     * @example
     * <pre>
     * $arr=array( 1=>'a', 2=>'b', );
     * self::hashToArray($arr,'id','name');
     * 结果为：
     * array(
     *    array('id'=>1,'name'=>'a'),
     *    array('id'=>2,'name'=>'b'),
     * );
     * </pre>
     * @param array $arr
     * @param string $keyName
     * @param string $valName
     * @return array
     */
    public static function hashToArray($arr, $keyName, $valName = null)
    {
        if (!is_array($arr) || empty($arr))
            return array();
        $result = array();
        foreach ($arr as $k => $v)
        {
            if (!empty($valName))
            {
                $result[] = array($keyName => $k, $valName => $v);
            }
            else if (is_array($v))
            {
                $v[$keyName] = $k;
                $result[] = $v;
            }
        }
        return $result;
    }

    /**
     * 将二维数组按照指定的键分组
     * @example
     * <pre>
     * $arr=array(
     *    array('id'=>1,'name'=>"aaa"),
     *    array('id'=>1,'name'=>"bbb"),
     *    array('id'=>3,'name'=>"ccc"),
     *    )
     * toHash($arr,'id');
     *  结果为：
     *  $arr=array(
     *    1=>array(
     *      array('id'=>1,'name'=>"aaa"),
     *      array('id'=>1,'name'=>"bbb")
     *          ),
     *    3=>array(array('id'=>3,'name'=>"ccc"),)
     *    )
     *  groupBy($arr,'id');
     *  结果为：
     *  $arr=array( 1=>"aaa", 2=>"bbb", 3=>"ccc" )
     *  </pre>
     * @param array $arr
     * @param string $key 分组的数组的键，支持使用.来进行多维的键的分组
     * @return array
     */
    public static function groupBy($arr, $key)
    {
        if (!is_array($arr) || empty($arr))
            return array();
        $result = array();
        foreach ($arr as $one)
        {
            $tmp = self::getRow($one, $key);
            if ($tmp == null)
                $tmp = "";
            if (!is_array($tmp))
            {
                $result[$tmp . ""][] = $one;
            }
        }
        return $result;
    }

    /**
     * @see self::groupBy
     */
    public static function toGroup($arr, $key)
    {
        return self::groupBy($arr, $key);
    }

    /**
     * 像使用sql的order by 一样 对一个多维数组进行排序
     * @example
     * <pre>
     * $arr=array(
      "a"=>array('a'=>1,'b'=>"ad",'c'=>array('d'=>'9')),
      "b"=>array('a'=>2,'b'=>"cd",'c'=>array('d'=>'12')),
      'c'=>array('a'=>2,'b'=>"dd",'c'=>array('d'=>'1')),
      'e'=>array('a'=>20,'b'=>"aa"),
      );
      self::orderBy($arr, "b desc");
      结果为：
      Array(
      'c'=>array('a'=>2,'b'=>"dd",'c'=>array('d'=>'1')),
      "b"=>array('a'=>2,'b'=>"cd",'c'=>array('d'=>'12')),
      "a"=>array('a'=>1,'b'=>"ad",'c'=>array('d'=>'9')),
      'e'=>array('a'=>20,'b'=>"aa"),
      )
     * </pre>
     * @param array $arr 待排序的数组
     * @param string $cond 排序条件 如 <font color=red>updateTime desc,uid asc,more.updateTime desc</font>
     */
    public static function orderBy(&$arr, $cond)
    {
        if (!is_array($arr))
            return false;
        if (empty($arr) || empty($cond))
            return true;

        $cond_arr = self::nameSplit($cond, ",");
        $code = "";
        foreach ($cond_arr as $_con)
        {
            $_sub_con_arr = preg_split("/\s+/", trim($_con));
            $_k_name = $_sub_con_arr[0];
            $_sort_type = isset($_sub_con_arr[1]) ? strtolower($_sub_con_arr[1]) : 'asc';
            $_k_name = implode("']['", self::nameSplit($_k_name));
            $a = "\$a['" . $_k_name . "']";
            $b = "\$b['" . $_k_name . "']";
            $c = $_sort_type == "desc" ? "<" : ">";
            $code.='if(' . $a . '!=' . $b . ')return ' . $a . $c . $b . ";\n";
        }
        $code.="return true;";
        $function = create_function('$a,$b', $code);
        if (!$function)
            return false;
        return @uasort($arr, $function);
    }

    /**
     * 对多维数组按照条件进行筛选 
     * 对于取非判断，若该项不存在也认为是真
     * @example
     * <pre>$arr=array(
      array('id'=>1,'name'=>"aaa"),
      array('id'=>2,'name'=>"bbb"),
      array('id'=>3,'name'=>"ccc"),
      array('id'=>'4','name'=>"ccc"),
      array('id'=>array('a','b'),'name'=>"ccc"),
      array('id'=>array('a','b'),'name'=>"ddd"),
      );
      <font color=blue>$cond="(id>=1 and id<2) and name=aaa or id.0=a or id==4";</font>
      $result= rArray::filter($arr, $cond); </pre>
      <div>
      <b>支持如下函数判断字段类型:</b>
      <span style='color:red'>isset,is_array,is_int,is_num,is_bool,is_double,is_integer,is_float,is_long,is_string,empty</span><br/>
      以及 <span style='color:red'>substr，strlen,count</span> 函数，
      并且可以使用<span style='color:red'>match</span>函数来进行正则匹配
      </div>
      cond demo:
      <ol style='color:blue'>
      <li>id in(1,2) and id not in(4)</li>
      <li>name in('aaa',"aaa")  and is_string(name)</li>
      <li>id!=1 and id<3 and substr(name,1,1)=d</li>
      <li>strlen(name)>=1 and strlen(name)!=2</li>
      <li>match(name,'a*') and id<2  使用通配符匹配</li>
      <li>!match(name,/a(.*)/) and id<3 使用正则表达式</li>
      </ol>
     * @param array $arr
     * @param string $cond 筛选条件,支持>=<、in、not in筛选 如 <font color=red>(id>=1 and id<2) and name=aaa or id.0=a or id==4 or id in (1)</font>
     * @return array
     */
    public static function filter($arr, $cond)
    {
        if (!is_array($arr) || empty($arr))
            return array();
        if (empty($cond))
            return $arr;

        $cond = " " . preg_replace("/[\(\)]/", " \\0 ", $cond) . " ";
        $cond_stage = array();

        $cond_str = $cond;

        //match in,not in
        $cond_str = preg_replace_callback("/\s(\S+?)\s*((\snot\s+)?in)\s*\((.+?)\)\s/", array('self', '_filter_callback_2'), $cond_str);
        self::_stage($cond_stage, $cond_str);

        //match function call
        $cond_str = preg_replace_callback("/\s(!?\s*\w+?)\s*\((.+?)(,.+?)?\)\s+((!?[>=<]=?)\s*([\"']?.+?[\"']?))?\s/", array('self', '_filter_callback_3'), $cond_str);
        self::_stage($cond_stage, $cond_str);

        //match <>=!
        $cond_str = preg_replace_callback("/\s(\S+?)\s*(!?[>=<]={0,2})\s*[\"']?(.+?)[\"']?\s/", array('self', '_filter_callback_1'), $cond_str);
        self::_stage($cond_stage, $cond_str);

        //将暂存的表达式还原
        foreach ($cond_stage as $_uid => $_stags)
        {
            for ($i = 0; $i < count($_stags); $i++)
            {
                $cond_str = preg_replace("#" . $_uid . "#", substr($_stags[$i], 1, -1), $cond_str, 1);
            }
        }
        if (empty($cond_str))
            return false;

        $function = create_function('$a', "return (" . $cond_str . ");");
        if (!$function)
            return false;
        $result = array_filter($arr, $function);
        return $result;
    }

    /**
     * 多处理过的筛选条件进行暂存
     * @param array $stage
     * @param string $cond_str
     */
    private static function _stage(&$stage, &$cond_str)
    {
        $reg = "/\(\(.+?\)\)/";
        if (preg_match_all($reg, $cond_str, $matches))
        {
            $uniqueId = "array_filter_" . uniqid(); //每暂存一次 使用一个新的uuid
            foreach ($matches[0] as $_t)
            {
                $stage[$uniqueId][] = $_t;
            }
            $cond_str = preg_replace($reg, $uniqueId, $cond_str);
        }
    }

    /**
     * 处理比较操作
     * @param array $matches
     * @return string
     */
    private static function _filter_callback_1($matches)
    {
//        print_r($matches);
        $name = '$a["' . implode('"]["', self::nameSplit($matches[1])) . '"]';
        $s = $matches[2] == "=" ? "==" : $matches[2];
        $val = is_numeric($matches[3]) ? $matches[3] : '"' . $matches[3] . '"';
        $call = $name . $s . $val;
        $is_not = substr($s, 0, 1) == "!";
        if (!$is_not)
            return " ((isset(" . $name . ") && " . $call . "))  ";
        return " ((!isset(" . $name . ") || " . $call . "))  ";
    }

    /**
     * 处理in,not in操作
     * @param array $matches
     * @return string
     */
    private static function _filter_callback_2($matches)
    {
//        print_r($matches);
        $name = '$a["' . implode('"]["', self::nameSplit($matches[1])) . '"]';
        $type = trim(preg_replace("/\s+/", " ", $matches[2]));
        $is_in = $type == "in";
        $vs = self::nameSplit($matches[4], ",");
        foreach ($vs as &$v)
        {
            $v = preg_replace("/^[\"']|[\"']$/", "", trim($v));
        }
        $vs_str = preg_replace("/([\n\r]\s+\d+\s*=>\s*)|[\n]/", "", var_export($vs, true));
//        print_r($vs_str);
        $call = ($is_in ? "in_array" : "!in_array") . "(" . $name . "," . $vs_str . ")";
        //in_array
        if ($is_in)
            return " ((isset(" . $name . ") && " . $call . "))  ";
        //not in array
        return " ((!isset(" . $name . ") || " . $call . "))  ";
    }

    /**
     * 处理函数调用
     * @param array $matches
     * @return string
     */
    private static function _filter_callback_3($matches)
    {
        self::trim($matches);
        $function_support_is = array('isset', 'is_array', 'is_int', 'is_num', 'is_bool', 'is_double', 'is_integer', 'is_float', 'is_long', 'is_string', 'empty');
//        print_r($matches);
        $funName = trim($matches[1]); //函数名称
        $paraName = trim($matches[2]); //参数变量名称
        $name = '$a["' . implode('"]["', self::nameSplit($paraName)) . '"]';

        $is_not = substr($funName, 0, 1) == "!";
        $funName_real = $is_not ? substr($funName, 1) : $funName; //去掉前面的！的函数名


        if (in_array($funName_real, $function_support_is))
        {
            return " (({$funName}({$name}))) ";
        }

        //处理 strlen(id)>1 、substr(id,1,2)=='a'
        $function_support_other = array('strlen', 'count', 'substr');
        if (in_array($funName, $function_support_other))
        {
            $paraMore = $matches[3]; //其他参数
            $t = $matches[5]; //操作符，如> = <
            $v = var_export($matches[6], true); //期望值
            if ($t == "=")
                $t = "==";
            return " (( {$funName}({$name} {$paraMore}){$t}{$v} )) ";
        }

        $not_pre = $is_not ? "!" : "";
        if ($funName_real == "match")
        {
            $con = preg_replace("/^\s*['\"]|['\"]\s*$/", "", ltrim($matches[3], ","));
            if (preg_match("/^\/.+\/$/", $con))
            {
                $con = preg_replace("/\s+([\(\)])\s+/", "\\1", $con);
                //匹配正则表达式
            }
            else if (false !== strpos($con, "*"))
            {
                $con = "/" . str_replace("*", ".*", $con) . "/";
            }
            else
            {
                $con = "";
            }
            return $con ? " (({$not_pre}preg_match('{$con}',{$name}))) " : "";
        }
    }

    /**
     * 将二维数组转换为树形结构
     * @example
     * <pre>
     *         $arr=array(
      array('id'=>1,'pid'=>0),
      array('id'=>2,'pid'=>0),
      array('id'=>3,'pid'=>1),
      array('id'=>4,'pid'=>3),
      array('id'=>5,'pid'=>1),
      );
      $result= self::toTree($arr, "id", 'pid','children');
      结果为:
      array (0 =>array (
      'id' => 1,
      'pid' => 0,
      'children' =>
      array (
      0 =>
      array (
      'id' => 3,
      'pid' => 1,
      'children' =>  array (
      0 => array ('id' => 4,'pid' => 3, 'children' => array (),),
      ),
      ),
      1 =>  array ('id' => 5,'pid' => 1, 'children' => array (),),
      ),
      ),
      1 => array ( 'id' => 2,'pid' => 0,'children' => array (),
      ))
     * </pre>
     * @param array $arr
     * @param string $idField  id字段名称
     * @param string $parentIdField 父id字段名称
     * @param string $childField 子节点名称
     * @return array
     */
    public static function toTree($arr, $idField, $parentIdField, $childField = 'nodes')
    {
        if (!is_array($arr) || empty($arr))
            return array();
        $map_index = self::toHash($arr, $idField, $parentIdField);
        $result = array();
        $index = 0;
        $childrens = array();
        foreach ($map_index as $id => $parentid)
        {
            if (!isset($map_index[$parentid]))
            {
                $result[] = $arr[$index];
            }
            else
            {
                $childrens[$id] = $arr[$index];
            }
            $index++;
        }
        foreach ($result as $i => $row)
        {
            $row[$childField] = self::_getAllChildren($childrens, $idField, $parentIdField, $childField, $row[$idField]);
            $result[$i] = $row;
        }
        return $result;
    }

    private static function _getAllChildren(&$arr, $idField, $parentIdField, $childField, $idValue)
    {
        $map = self::groupBy($arr, $parentIdField);
        if (isset($map[$idValue]))
        {
            $cs = $map[$idValue];
            foreach ($cs as $i => $v)
            {
                unset($arr[$v[$idField]]);
            }
            foreach ($cs as &$sub)
            {
                $sub[$childField] = self::_getAllChildren($arr, $idField, $parentIdField, $childField, $sub[$idField]);
            }
            return $cs;
        }
        else
        {
            return array();
        }
    }

    /**
     * 从多维数组中筛选出需要的列
     * @param array $arr
     * @param string $select 要筛选出来的字段名称，支持*通配符或者正则，如 <font color=blue>id.0 as id0,n*me,i,a{2\,3}/e</font>
     * @return array
     * @example<pre>
     * $arr=array(
      array("aaaa"=>'1'),
      array("aaa"=>'2'),
      array("aa"=>'3'),
      );
      $res=self::select($arr, "a{2\,3}/e");
      $result=array (0 =>array (),1 =>array ('aaa' => '2',),2 =>array ('aa' => '3',),);
     * </pre>
     */
    public static function select($arr, $select)
    {
        if (empty($arr) || !is_array($arr))
            return array();
        $select = trim($select);
        if (empty($select) || $select == "*")
            return $arr;

        $fiels = self::nameSplit($select, ",");
        self::trim($fiels);
        $result = array();
        foreach ($arr as $i => $row)
        {
            $data = array();
            foreach ($fiels as $field)
            {
                if (preg_match("/^(\S+)\s+as\s+(\S+)$/", $field, $matches))
                {
                    $data[$matches[2]] = self::getRow($row, $matches[1]);
                }
                else if (false !== strpos($field, "*") || preg_match("/.+\/e$/", $field))
                {
                    $eg_key = str_replace(array("*", "/e"), array("\S+", ""), $field);
                    foreach ($row as $_k => $_v)
                    {
                        if (preg_match("/^{$eg_key}$/", $_k))
                        {
                            $data[$_k] = $_v;
                        }
                    }
                }
                else
                {
                    $data[$field] = isset($row[$field]) ? $row[$field] : null;
                }
            }
            $result[$i] = $data;
        }
        return $result;
    }

    /**
     * 使用完整的sql语句来对数据进行筛选、排序、分组等
     * 该函数是select,orderBy,groupBy这个几个函数的一个封装
     * @tutorial
     * select、order by、group by 的语法参考具体的 select,orderBy,groupBy函数
     * 不支持limit\like 等操作
     * @param array $arr
     * @param string $sql 如 select id,name as 名字 where id >1 order by id desc group by id
     * @return array
     */
    public static function selectByFullSql($arr, $sql)
    {
        if (!is_array($arr) || empty($arr))
            return array();
        $sql.=" ";
        preg_match_all("/^\s*select\s+(.+?)\s*(where\s+(.+?))?(\s+order\s*by\s+(.+?))?(\s+group\s*by\s+(.+?))?$/i", $sql, $matches, PREG_SET_ORDER);
        $match = $matches[0];
        self::trim($match);
        if (!empty($match[3]))
            $arr = self::filter($arr, $match[3]);
        if (!empty($match[5]))
            self::orderBy($arr, $match[5]);
        $arr = self::select($arr, $match[1]);
        if (!empty($match[7]))
            $arr = self::groupBy($arr, trim($match[7]));
        return $arr;
    }

    /**
     * merge deep
     * 支持多个参数，一次合并多个数组,默认对于值为array()、''、null的不会进行merge
     * 若最后一个参数为true,则对上述空值也会进行merge
     * @return boolean|mixed|array
     * @example
     * <pre>
     *  $a=array('a'=>array('b'=>'c'),'d'=>'d','f'=>array('f1'),'e'=>array('e1'=>array('ee1'=>'ea')));
      $b=array('a'=>array('c'=>'d'),'d'=>'','f'=>array(),'e'=>array('e1'=>array('ee1'=>'ea1')));
      $res=rArray::mergeDeep($a,$b);
      结果为：
      array ('a' =>array ('b' => 'c','c' => 'd',),'d' => 'd','f' =>array (0 => 'f1',),'e' =>array ('e1' =>array ('ee1' => 'ea1',),),);

      $res=rArray::mergeDeep($a,$b,<font color=red>true</font>);
      结果为：
      array ('a' =>array ('b' => 'c','c' => 'd',),'d' => '','f' =>array (),'e' =>array ('e1' =>array ('ee1' => 'ea1',),),);
      </pre>
     */
    public static function mergeDeep()
    {
        $num = func_num_args();
        if ($num == 0)
            return false;
        if ($num == 1)
            return func_get_arg(0);
        $lastVal = func_get_arg($num - 1);
        if (is_bool($lastVal) && $num == 2)
            return func_get_arg(0);
        $extend_empty = is_bool($lastVal) ? $lastVal : false;

        $args_arr = func_get_args();
        $first = array_shift($args_arr);
        if (is_bool($lastVal))
            array_pop($args_arr);
        foreach ($args_arr as $arr)
        {
            if (!is_array($arr))
                continue;
            foreach ($arr as $k => $v)
            {
                $is_empty = ($v == "" || $v == array() || $v == null);
                if (($extend_empty && $is_empty ) || (!$is_empty && !is_array($v)))
                {
                    $first[$k] = $v;
                }
                else if (is_array($v))
                {
                    $first[$k] = self::mergeDeep($first[$k], $v, $extend_empty);
                }
            }
        }
        return $first;
    }

    /**
     * Returns an array with values for given keys converted to numeric type.
     * Only actual numeric values are converted.(so null is still null)
     * @param array $array
     * @param array $keys
     * @return array
     * @exmaple
     * me(array('a' => '1', 'b' => '2'), array('a')) //=> array('a' => 1, 'b' => '2')
     */
    public static function numerify($array, $keys)
    {
        if (empty($array))
            return $array;
        $a = array();
        foreach ($array as $k => $v)
        {
            if (in_array($k, $keys))
            {
                $v = self::_numerifyScalar($v);
            }
            $a[$k] = $v;
        }
        return $a;
    }

    private static function _numerifyScalar($v)
    {
        if (is_numeric($v))
            return $v + 0;
        return $v;
    }

    /**
     * Apply numerify to each element in $array
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function mapNumerify($array, $keys)
    {
        if (empty($array))
            return $array;
        $a = array();
        foreach ($array as $k => $v)
        {
            $a[$k] = self::numerify($v, $keys);
        }
        return $a;
    }

    public static function toPath($all, $idField, $parentIdField)
    {
        $map = self::toHash($all, $idField, $parentIdField);
        $result = array();
        foreach ($map as $c => $p)
        {
            $result[$c] = self::_getPath($map, $p, $result);
            $result[$c][] = $c;
            $result[$c] = array_values(array_unique($result[$c]));
        }
        return $result;
    }

    private static function _getPath($map, $pid, &$rs)
    {
        if (empty($pid))
            return array();
        $result = array($pid);
        $_pid = empty($map[$pid]) ? "" : $map[$pid];
        if (empty($_pid))
            return $result;
        $result = array_merge(array($_pid), $result);
        if (isset($rs[$_pid]))
        {
            $cs = $rs[$_pid];
        }
        else
        {
            $cs = self::_getPath($map, $_pid, $rs);
        }
        $result = array_merge($cs, $result);
        return $result;
    }


    /**
     * 对数组按照指定key进行排序
     * @param array  $array
     * @param string $theKey
     * @param string $sort
     * @return array
     */
    public static function SortByKey(array $array, string $theKey, $sort='asc') {
        $newArr = $valArr = array();
        foreach ($array as $key=>$value)
        {
            $valArr[$key] = $value[$theKey];
        }
        ($sort == 'asc') ?  asort($valArr) : arsort($valArr);  //先利用keys对数组排序，目的是把目标数组的key排好序
        reset($valArr);    //指针指向数组第一个值
        foreach($valArr as $key=>$value)
        {
            $newArr[] = $array[$key];
        }
        return $newArr;
    }

}