<?php
/**
 * Created by PhpStorm.
 * User: HuangXiao
 * Date: 2019/3/26
 * Time: 11:20
 */

class Csv
{
    /**
     * @param string $filename
     * @param array $header
     * @param array $data
     */
    public static function OutPut(string $filename, array $header=[], array $data=[])
    {
        ob_end_clean();  //清除内存
        ob_start();
        $filename = $filename.'.csv';
        $content = join(",",$header);
        $content = iconv('utf-8','gbk',$content);
        $index = 0;
        foreach ($data as $item)
        {
            if($index==1000){ //每次写入1000条数据清除内存
                $index=0;
                ob_flush();//清除内存
                flush();
            }
            $index++;
            $line = PHP_EOL.join(",",$item);
            $line = iconv('utf-8','gbk',$line);
            $content .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        header('Content-Length: '.strlen($content));
        echo $content;
        ob_flush();
        flush();
        ob_end_clean();
        exit;
    }
}