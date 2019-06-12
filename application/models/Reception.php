<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:51
 */
class ReceptionModel
{
    private static $table = 'reception_num';

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
    

    public static  function getReception()
    {
        $data['year_reception'] = self::getYearReception();
        $data['mouth_reception'] = self::getMonthReception();
        $data['day_reception'] = self::getDayReception();
        return $data;
    }

    public static function getDayReception()
    {
        $date = date('Y-m-d');
        $data = Db::Table(static::$table)
            ->Col(['scenic_spot_id,num'])
            ->Where("date = '{$date}'")
            ->Find();
        $outData = [];
        foreach ($data as $k=>$v){
            $outData[$v['scenic_spot_id']] = $v;
        }

        if(array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']+$outData[3]['num']];
        }
        else if (array_key_exists(1,$outData) && !array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']];
        }
        else if (!array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[3]['num']];
        }
        else
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>0];
        }
        $outputData [1]= array_key_exists(2,$outData) ? $outData[2]: ['scenic_spot_id'=>2,'num'=>0];
        $outputData [2]= array_key_exists(4,$outData) ? ['scenic_spot_id'=>3,'num'=>$outData[4]['num']]:
            ['scenic_spot_id'=>3,'num'=>0];
        return $outputData;
    }

    public static function getMonthReception()
    {
        $startDay = date('Y-m')."-01";
        $endDay   = date('Y-m-d');
        $data = Db::Table(static::$table)
            ->Col(['scenic_spot_id,sum(num) as num'])
            ->GroupBy('scenic_spot_id')
            ->Where("date between '{$startDay}' and '{$endDay}'")
            ->Find();
        $outData = [];
        foreach ($data as $k=>$v){
            $outData[$v['scenic_spot_id']] = $v;
        }

        if(array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']+$outData[3]['num']];
        }
        else if (array_key_exists(1,$outData) && !array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']];
        }
        else if (!array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[3]['num']];
        }
        else
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>0];
        }
        $outputData [1]= array_key_exists(2,$outData) ? $outData[2]: ['scenic_spot_id'=>2,'num'=>0];
        $outputData [2]= array_key_exists(4,$outData) ? ['scenic_spot_id'=>3,'num'=>$outData[4]['num']]:
            ['scenic_spot_id'=>3,'num'=>0];
        return $outputData;
    }

    public static function getYearReception()
    {
        $startDay = date('Y')."-01-01";
        $endDay   = date('Y-m-d');
        $data = Db::Table(static::$table)
            ->Col(['scenic_spot_id,sum(num) as num'])
            ->GroupBy('scenic_spot_id')
            ->Where("date between '{$startDay}' and '{$endDay}'")
            ->Find();
        $outData = [];
        foreach ($data as $k=>$v){
            $outData[$v['scenic_spot_id']] = $v;
        }

        if(array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']+$outData[3]['num']];
        }
        else if (array_key_exists(1,$outData) && !array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']];
        }
        else if (!array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[3]['num']];
        }
        else
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>0];
        }
        $outputData [1]= array_key_exists(2,$outData) ? $outData[2]: ['scenic_spot_id'=>2,'num'=>0];
        $outputData [2]= array_key_exists(4,$outData) ? ['scenic_spot_id'=>3,'num'=>$outData[4]['num']]:
            ['scenic_spot_id'=>3,'num'=>0];
        return $outputData;
    }

    public static function getReceptionCompared()
    {
        $beforeThereYear = date('Y',strtotime("-3 year"));
        $beforeTwoYear = date('Y',strtotime("-2 year"));
        $beforeOneYear = date('Y',strtotime("-1 year"));
        $nowYear = date('Y');
        $dataBeforeThereYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(num) as num'])
            ->GroupBy('month')
            ->Where("date between '{$beforeThereYear}-01-01' and '{$beforeTwoYear}-01-01' ")
            ->Find();
        $dataBeforeTwoYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(num) as num'])
            ->GroupBy('month')
            ->Where("date between '{$beforeTwoYear}-01-01' and '{$beforeOneYear}-01-01' ")
            ->Find();
        $dataBeforeOneYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(num) as num'])
            ->GroupBy('month')
            ->Where("date between '{$beforeOneYear}-01-01' and '{$nowYear}-01-01' ")
            ->Find();
        $endDay   = date('Y-m-d',strtotime("-1 day"));
        $dataNowYear = Db::Table(static::$table)
            ->Col(['month(date) as month,sum(num) as num'])
            ->GroupBy('month')
            ->Where("date between '{$nowYear}-01-01' and '{$endDay}'")
            ->Find();
        $data = [];$nowMonth = date('m');
        for ($i=1;$i<=12;$i++){
            $data[$beforeThereYear][$i]['num'] =
                array_key_exists($i-1,$dataBeforeThereYear) ? $dataBeforeThereYear[$i-1]['num'] : 0;
            $data[$beforeTwoYear][$i]['num'] =
                array_key_exists($i-1,$dataBeforeTwoYear) ? $dataBeforeTwoYear[$i-1]['num'] : 0;
            $data[$beforeOneYear][$i]['num'] =
                array_key_exists($i-1,$dataBeforeOneYear) ? $dataBeforeOneYear[$i-1]['num'] : 0;
            if($i < $nowMonth)
            {
                $data[$nowYear][$i]['num'] = array_key_exists($i-1,$dataNowYear) ? $dataNowYear[$i-1]['num'] : 0;
            }
        }

        for ($j=1;$j<=12;$j++)
        {
            $addBeforeTwoYear = $data[$beforeTwoYear][$j]['num']-
                $data[$beforeThereYear][$j]['num'];

            $addBeforeOneYear = $data[$beforeOneYear][$j]['num']-$data[$beforeTwoYear][$j]['num'];
            $addNowYear = $data[$nowYear][$j]['num']-$data[$beforeOneYear][$j]['num'];
            $data[$beforeTwoYear][$j]['add_rate'] =
                $data[$beforeThereYear][$j]['num'] == 0 ? 10000 : round($addBeforeTwoYear/$data[$beforeThereYear][$j]['num'],3);
            $data[$beforeOneYear][$j]['add_rate'] =
                $data[$beforeTwoYear][$j]['num'] == 0 ? 10000 : round($addBeforeOneYear/$data[$beforeTwoYear][$j]['num'],3);
            if($j < $nowMonth)
            {
                $data[$nowYear][$j]['add_rate'] =
                    $data[$beforeOneYear][$j]['num'] == 0 ? 10000 : round($addNowYear/$data[$beforeOneYear][$j]['num'],3);
            }
        }
        unset($data[$beforeThereYear]);
//        unset($data[$beforeTwoYear]);
        return $data;
    }

    public static function getReceptionByTime($startTime,$endTime)
    {
        $data = Db::Table(static::$table)
            ->Col(['scenic_spot_id,sum(num) as num'])
            ->Where("date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('scenic_spot_id')
            ->Find();
        foreach ($data as $k=>$v){
            $outData[$v['scenic_spot_id']] = $v;
        }

        if(array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']+$outData[3]['num']];
        }
        else if (array_key_exists(1,$outData) && !array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']];
        }
        else if (!array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[3]['num']];
        }
        else
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>0];
        }
        $outputData [1]= array_key_exists(2,$outData) ? $outData[2]: ['scenic_spot_id'=>2,'num'=>0];
        $outputData [2]= array_key_exists(4,$outData) ? ['scenic_spot_id'=>3,'num'=>$outData[4]['num']]:
            ['scenic_spot_id'=>3,'num'=>0];

        $total_num = array_sum(array_column($outputData,'num'));
        $outputData[0]['rate'] = $total_num ? round($outputData[0]['num']/$total_num,3) : 0;
        $outputData[1]['rate'] = $total_num ? round($outputData[1]['num']/$total_num,3) : 0;
        $outputData[2]['rate'] = $total_num ? round($outputData[2]['num']/$total_num,3) : 0;

        return $outputData;
    }

    public static function getDateReception()
    {
        $date = date('Y-m-d');
        $data = Db::Table(static::$table)
            ->Col(['scenic_spot_id,num'])
            ->Where("date = '{$date}'")
            ->Find();
        foreach ($data as $k=>$v){
            $outData[$v['scenic_spot_id']] = $v;
        }

        if(array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']+$outData[3]['num']];
        }
        else if (array_key_exists(1,$outData) && !array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[1]['num']];
        }
        else if (!array_key_exists(1,$outData) && array_key_exists(3,$outData))
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>$outData[3]['num']];
        }
        else
        {
            $outputData [0] = ['scenic_spot_id'=>1,'num'=>0];
        }
        $outputData [1]= array_key_exists(2,$outData) ? $outData[2]: ['scenic_spot_id'=>2,'num'=>0];
        $outputData [2]= array_key_exists(4,$outData) ? ['scenic_spot_id'=>3,'num'=>$outData[4]['num']]:
            ['scenic_spot_id'=>3,'num'=>0];

        $total_num = array_sum(array_column($outputData,'num'));
        $outputData[0]['rate'] = $total_num ? round($outputData[0]['num']/$total_num,3) : 0;
        $outputData[1]['rate'] = $total_num ? round($outputData[1]['num']/$total_num,3) : 0;
        $outputData[2]['rate'] = $total_num ? round($outputData[2]['num']/$total_num,3) : 0;

        return $outputData;
    }

}