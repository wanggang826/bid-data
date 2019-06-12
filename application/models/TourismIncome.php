<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:54
 */
class TourismIncomeModel
{
    private static $table = 'tourism_income';

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

    public static function getIncomeData()
    {
        $beforeThereYear = date('Y',strtotime("-3 year"));
        $beforeTwoYear = date('Y',strtotime("-2 year"));
        $beforeOneYear = date('Y',strtotime("-1 year"));
        $nowYear = date('Y');
        $dataBeforeThereYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(income) as income'])
            ->GroupBy('month')
            ->Where("date between '{$beforeThereYear}-01-01' and '{$beforeTwoYear}-01-01' ")
            ->Find();
        $dataBeforeTwoYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(income) as income'])
            ->GroupBy('month')
            ->Where("date between '{$beforeTwoYear}-01-01' and '{$beforeOneYear}-01-01' ")
            ->Find();
        $dataBeforeOneYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(income) as income'])
            ->GroupBy('month')
            ->Where("date between '{$beforeOneYear}-01-01' and '{$nowYear}-01-01' ")
            ->Find();
        $endDay   = date('Y-m-d',strtotime("-1 day"));
        $dataNowYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(income) as income'])
            ->GroupBy('month')
            ->Where("date between '{$nowYear}-01-01' and '{$endDay}'")
            ->Find();
        $data = [];$nowMonth = date('m');
        for ($i=1;$i<=12;$i++)
        {
            $data[$beforeThereYear][$i]['income'] = array_key_exists($i-1,$dataBeforeThereYear) ? $dataBeforeThereYear[$i-1]['income'] : 0;
            $data[$beforeTwoYear][$i]['income'] = array_key_exists($i-1,$dataBeforeTwoYear) ? $dataBeforeTwoYear[$i-1]['income'] : 0;
            $data[$beforeOneYear][$i]['income'] = array_key_exists($i-1,$dataBeforeOneYear) ? $dataBeforeOneYear[$i-1]['income'] : 0;
            if($i < $nowMonth) {
                $data[$nowYear][$i]['income'] = array_key_exists($i - 1, $dataNowYear) ? $dataNowYear[$i - 1]['income'] : 0;
            }
        }
        for ($j=1;$j<=12;$j++)
        {
            $addBeforeTwoYear = $data[$beforeTwoYear][$j]['income']-$data[$beforeThereYear][$j]['income'];
            $addBeforeOneYear = $data[$beforeOneYear][$j]['income']-$data[$beforeTwoYear][$j]['income'];
            $addNowYear = $data[$nowYear][$j]['income']-$data[$beforeOneYear][$j]['income'];
            $data[$beforeTwoYear][$j]['add_rate'] =
                $data[$beforeThereYear][$j]['income'] == 0 ? 10000 : round($addBeforeTwoYear/$data[$beforeThereYear][$j]['income'],3);
            $data[$beforeOneYear][$j]['add_rate'] =
                $data[$beforeTwoYear][$j]['income'] == 0 ? 10000 : round($addBeforeOneYear/$data[$beforeTwoYear][$j]['income'],3);
            if($j < $nowMonth){
                $data[$nowYear][$j]['add_rate'] =
                    $data[$beforeOneYear][$j]['income'] == 0 ? 10000 : round($addNowYear/$data[$beforeOneYear][$j]['income'],3);
            }
        }
        unset($data[$beforeThereYear]);
//        unset($data[$beforeTwoYear]);
        return $data;
    }
}
