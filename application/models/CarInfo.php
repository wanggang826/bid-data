<?php
/**
 * User: wgg
 * Date: 19-4-14
 * Time: 下午2:06
 */
class CarInfoModel
{
    private static $table = 'car_info';

    public static function Inserts($values)
    {
        $sql = "insert into  `car_info`(`date`,`scenic_spot_id`,`car_number`) values ".$values;
        $result = Db::Table(self::$table)->executeSql($sql);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function ChangeAnalysisid($ids = []){
        $ids = $ids ? '('.implode(',',$ids).')' : '';
        $result = Db::Table(self::$table)
            ->Where('id in '.$ids)
            ->Update(['is_analysisided'=>1]);
        if ($result['result']) {
            return true;
        }
        return false;
    }

    public static function getDayCarData()
    {
        $carData = Db::Table(self::$table)
            ->Col(['id,date,car_number'])
            ->Where("is_analysisided = 0")
            ->find();

        $outData ['ids'] = array_column($carData,'id');

        $dateCarList = [];
        //数据去重
        $carData = array_unique($carData,SORT_REGULAR);
        foreach ($carData as $k=>$v)
        {
            $dateCarList[$v['date']][]= substr($v['car_number'],0,3);
        }

        $data = [];
        foreach ($dateCarList as $k1=>$v1)
        {
            $dateArray = ['date'=>$k1];
            $count_array = array_count_values($v1);
            $count_data  = array_merge($dateArray,$count_array);
            $data[$k1] = [
                'date' => $k1,
                'shanxi_car_count' => array_key_exists('陕',$count_data) ? $count_data['陕']: 0,
                'beijing_car_count'=> array_key_exists('京',$count_data) ? $count_data['京']: 0,
                'tianjin_car_count'=> array_key_exists('津',$count_data) ? $count_data['津']: 0,
                'hebei_car_count'=> array_key_exists('冀',$count_data) ? $count_data['冀']: 0,
                'sanxi_car_count'=> array_key_exists('晋',$count_data) ? $count_data['晋']: 0,
                'neimeng_car_count'=> array_key_exists('蒙',$count_data) ? $count_data['蒙']: 0,
                'liaoning_car_count'=> array_key_exists('辽',$count_data) ? $count_data['辽']: 0,
                'jilin_car_count'=> array_key_exists('吉',$count_data) ? $count_data['吉']: 0,
                'heilongjiang_car_count'=> array_key_exists('黑',$count_data) ? $count_data['黑']: 0,
                'shanghai_car_count'=> array_key_exists('沪',$count_data) ? $count_data['沪']: 0,
                'jiangsu_car_count'=> array_key_exists('苏',$count_data) ? $count_data['苏']: 0,
                'zhejiang_car_count'=> array_key_exists('浙',$count_data) ? $count_data['浙']: 0,
                'anhui_car_count'=> array_key_exists('皖',$count_data) ? $count_data['皖']: 0,
                'fujian_car_count'=> array_key_exists('闽',$count_data) ? $count_data['闽']: 0,
                'jiangxi_car_count'=> array_key_exists('赣',$count_data) ? $count_data['赣']: 0,
                'shandong_car_count'=> array_key_exists('鲁',$count_data) ? $count_data['鲁']: 0,
                'henan_car_count'=> array_key_exists('豫',$count_data) ? $count_data['豫']: 0,
                'hubei_car_count'=> array_key_exists('鄂',$count_data) ? $count_data['鄂']: 0,
                'hunan_car_count'=> array_key_exists('湘',$count_data) ? $count_data['湘']: 0,
                'guangdong_car_count'=> array_key_exists('粤',$count_data) ? $count_data['粤']: 0,
                'guangxi_car_count'=> array_key_exists('桂',$count_data) ? $count_data['桂']: 0,
                'hainan_car_count'=> array_key_exists('琼',$count_data) ? $count_data['琼']: 0,
                'chongqing_car_count'=> array_key_exists('渝',$count_data) ? $count_data['渝']: 0,
                'sichuan_car_count'=> array_key_exists('川',$count_data) ? $count_data['川']: 0,
                'guizhou_car_count'=> array_key_exists('贵',$count_data) ? $count_data['贵']: 0,
                'yunnan_car_count'=> array_key_exists('云',$count_data) ? $count_data['云']: 0,
                'xizang_car_count'=> array_key_exists('藏',$count_data) ? $count_data['藏']: 0,
                'gansu_car_count'=> array_key_exists('甘',$count_data) ? $count_data['甘']: 0,
                'qinghai_car_count'=> array_key_exists('青',$count_data) ? $count_data['青']: 0,
                'ningxia_car_count'=>array_key_exists('宁',$count_data) ? $count_data['宁']: 0,
                'xinjiang_car_count'=> array_key_exists('新',$count_data) ? $count_data['新']: 0,
                'xianggang_car_count'=> array_key_exists('港',$count_data) ? $count_data['港']: 0,
                'taiwan_car_count'=> array_key_exists('台',$count_data) ? $count_data['台']: 0,
                'aomen_car_count'=> array_key_exists('澳',$count_data) ? $count_data['澳']: 0
            ];
        }
        $outData['car_data'] = $data;
        return $outData;
    }

    public static function getSevenDayPark()
    {
        $date = date('Y-m-d');
        $beforeOneDay = date('Y-m-d',(strtotime($date)-1*86400));
        $beforeTwoDay = date('Y-m-d',(strtotime($date)-2*86400));
        $beforeThreeDay = date('Y-m-d',(strtotime($date)-3*86400));
        $beforeFourDay = date('Y-m-d',(strtotime($date)-4*86400));
        $beforeFiveDay = date('Y-m-d',(strtotime($date)-5*86400));
        $beforeSixDay = date('Y-m-d',(strtotime($date)-6*86400));
        $park1Data = Db::Table(self::$table)
            ->Col(['date,count(car_number) as count'])
            ->Where("scenic_spot_id = 1 and date between '{$beforeSixDay}' and '{$date}'")
            ->GroupBy('date')
            ->Find();
        foreach ($park1Data as $k => $v)
        {
            $outPark1Data[$v['date']] = $v;
        }
        $outPark1Data[$beforeSixDay] = array_key_exists($beforeSixDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeSixDay]['count']]:[ 'count'=>0];

        $outPark1Data[$beforeFiveDay] = array_key_exists($beforeFiveDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeFiveDay]['count']]:[ 'count'=>0];

        $outPark1Data[$beforeFourDay] = array_key_exists($beforeFourDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeFourDay]['count']]:[ 'count'=>0];

        $outPark1Data[$beforeThreeDay] = array_key_exists($beforeThreeDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeThreeDay]['count']]:[ 'count'=>0];

        $outPark1Data[$beforeTwoDay] = array_key_exists($beforeTwoDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeTwoDay]['count']]:[ 'count'=>0];

        $outPark1Data[$beforeOneDay] = array_key_exists($beforeOneDay,$outPark1Data) ?
            ['count'=>$outPark1Data[$beforeOneDay]['count']]:[ 'count'=>0];

        $outPark1Data[$date] = array_key_exists($date,$outPark1Data) ?
            ['count'=>$outPark1Data[$date]['count']]:[ 'count'=>0];

        $park2Data = Db::Table(self::$table)
            ->Col(['date,count(car_number) as count'])
            ->Where("scenic_spot_id = 2 and date between '{$beforeSixDay}' and '{$date}'")
            ->GroupBy('date')
            ->Find();

        foreach ($park2Data as $k => $v)
        {
            $outPark2Data[$v['date']] = $v;
        }
        $outPark2Data[$beforeSixDay] = array_key_exists($beforeSixDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeSixDay]['count']]:[ 'count'=>0];

        $outPark2Data[$beforeFiveDay] = array_key_exists($beforeFiveDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeFiveDay]['count']]:[ 'count'=>0];

        $outPark2Data[$beforeFourDay] = array_key_exists($beforeFourDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeFourDay]['count']]:[ 'count'=>0];

        $outPark2Data[$beforeThreeDay] = array_key_exists($beforeThreeDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeThreeDay]['count']]:[ 'count'=>0];

        $outPark2Data[$beforeTwoDay] = array_key_exists($beforeTwoDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeTwoDay]['count']]:[ 'count'=>0];

        $outPark2Data[$beforeOneDay] = array_key_exists($beforeOneDay,$outPark2Data) ?
            ['count'=>$outPark2Data[$beforeOneDay]['count']]:[ 'count'=>0];

        $outPark2Data[$date] = array_key_exists($date,$outPark2Data) ?
            ['count'=>$outPark2Data[$date]['count']]:[ 'count'=>0];
        ksort($outPark1Data);
        ksort($outPark2Data);
        return ['dxgParkData'=>$outPark1Data,'hhlParkData'=>$outPark2Data];
    }

    public static function getParkByTime($startTime,$endTime)
    {
        $diffTime = strtotime($endTime) - strtotime($startTime);
        $day = $diffTime/86400+1;
        $park1Data = Db::Table(self::$table)
            ->Col(['date,count(car_number) as count'])
            ->Where("scenic_spot_id = 1 and date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('date')
            ->Find();
        $park2Data = Db::Table(self::$table)
            ->Col(['date,count(car_number) as count'])
            ->Where("scenic_spot_id = 2 and date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('date')
            ->Find();

        foreach ($park1Data as $k1=>$v1)
        {
            $outPark1Data[$v1['date']] = $v1;
        }

        foreach ($park2Data as $k2=>$v2)
        {
            $outPark2Data[$v2['date']] = $v2;
        }

        for ($i=0;$i<$day;$i++)
        {
            $dateList[$i] = date('Y-m-d',strtotime($startTime)+$i*86400);
        }

        foreach ($dateList as $k => $v)
        {
            $outPark1Data[$v] = array_key_exists($v,$outPark1Data) ?
                ['count'=>$outPark1Data[$v]['count']]:[ 'count'=>0];
            $outPark2Data[$v] = array_key_exists($v,$outPark2Data) ?
                ['count'=>$outPark2Data[$v]['count']]:[ 'count'=>0];
        }

        ksort($outPark1Data);
        ksort($outPark2Data);
        return ['dxgParkData'=>$outPark1Data,'hhlParkData'=>$outPark2Data];
    }
}