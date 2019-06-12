<?php
/**
 * Created by PhpStorm.
 * User: HuangXiao
 * Date: 2019/4/9
 * Time: 11:27
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class Phpspreadsheet
 */
class Phpspreadsheet
{
    /**
     * 导出excel表
     * $data：要导出excel表的数据，接受一个二维数组
     * $filename：excel表的表名
     * $header：excel表的表头，接受一个一维数组
     * 备注：此函数缺点是，表头（对应列数）不能超过26；
     * 循环不够灵活，一个单元格中不方便存放两个数据库字段的值
     * @param $filename
     * @param $header
     * @param $data
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function OutPut( string $filename, array $header, array $data )
    {
        $count = count($header);  //计算表头数量

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 65; $i < $count + 65; $i++)
        {
            //数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $header[$i - 65]);
            //$sheet->getColumnDimension(strtoupper(chr($i)) . '1')->setWidth(18);
            $sheet->getStyle(strtoupper(chr($i)) . '1')->getFont()->setSize(11)->setBold(true);
        }

        foreach ($data as $key => $item)
        {
            //循环设置单元格：
            //$key+2,因为第一行是表头，所以写到表格时   从第二行开始写
            for ($i = 65; $i < $count + 65; $i++)
            {
                //数字转字母从65开始：
                $sheet->setCellValue(strtoupper(chr($i)) . ($key + 2), $item[$i - 65]);
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setWidth(20); //固定列宽
            }
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
}