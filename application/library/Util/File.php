<?php
/**
 * Created by PhpStorm.
 * User: huangshaowen
 * Date: 2015/10/26
 * Time: 19:15
 */

class Util_File {

    /**
     * 创建目录的函数
     */
    public static function makeDir($dir, $mode=0755, $recursive=true)
    {
        if (!$dir)
        {
            return false;
        }
        $dir = str_replace('\\', '/', $dir);
        if (file_exists($dir))
        {
            return true;
        }
        $return = false;
        $old_umask = umask(0);
        if (!$recursive)
        {
            $return = mkdir($dir, $mode);
        }
        else
        {
            $return = mkdir($dir, $mode, $recursive);

        }
        umask($old_umask);
        return $return;
    }
}