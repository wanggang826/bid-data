<?php
/**
 * User: wgg
 * Date: 19-4-14
 * Time: 下午1:48
 */
class VisitorInfoModel
{
    private static $table = 'visitor_info';

    public static function Inserts($values)
    {
        $sql = "insert into  `visitor_info`(`date`,`id_card`,`birthday`,`sex`,`travel_agency`,`group_num`,`province`) values ".$values;
        $result = Db::Table(self::$table)->executeSql($sql);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function Detail($where)
    {
        return Db::Table(static::$table)->Where($where)->Get();
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

    public static function getDayIdCardData()
    {
        $visitorInfoData = Db::Table(self::$table)
            ->Col(['id,date,id_card,province,birthday,sex'])
            ->Where(" is_analysisided = 0")
            ->find();

        $ids = array_column($visitorInfoData,'id');
        //数据去重
        $visitorInfoData = array_unique($visitorInfoData,SORT_REGULAR);

        $dateVisitorInfo = [];
        foreach ($visitorInfoData as $key=>$value)
        {
            $dateVisitorInfo[$value['date']][]= $value;
        }
        //按日期统计 性别数量  年龄段数量
        $visitorAttData = [];
        foreach ($dateVisitorInfo as $k=>$v)
        {
            $visitorAttData[$k]['date'] = $k;
            $sexData = array_count_values(array_column($v,'sex'));
            $visitorAttData[$k]['man_count']   = array_key_exists('男',$sexData) ? $sexData['男']:0;
            $visitorAttData[$k]['women_count'] = array_key_exists('女',$sexData) ? $sexData['女']:0;

            $birthdayData = array_column($v,'birthday');
            $visitorAttData[$k]['age1_count']= 0;
            $visitorAttData[$k]['age2_count']= 0;
            $visitorAttData[$k]['age3_count']= 0;
            $visitorAttData[$k]['age4_count']= 0;
            $visitorAttData[$k]['age5_count']= 0;
            $visitorAttData[$k]['age6_count']= 0;
            $visitorAttData[$k]['age7_count']= 0;

            foreach ($birthdayData as $k1=>$v1)
            {
                $age = self::getAge($v1);
                if($age < 18)
                {
                    $visitorAttData[$k]['age1_count'] += 1;
                }elseif($age >=18 && $age < 25)
                {
                    $visitorAttData[$k]['age2_count'] += 1;
                }elseif($age >=25 && $age < 35)
                {
                    $visitorAttData[$k]['age3_count'] += 1;
                }elseif($age >=35 && $age < 45)
                {
                    $visitorAttData[$k]['age4_count'] += 1;
                }
                elseif($age >=45 && $age < 55)
                {
                    $visitorAttData[$k]['age4_count'] += 1;
                }elseif($age >=55 && $age <= 60)
                {
                    $visitorAttData[$k]['age6_count'] += 1;
                }elseif($age > 60)
                {
                    $visitorAttData[$k]['age7_count'] += 1;
                }

            }
        }
        //统计客源地频率
        //$provinceList = array_column($visitorInfoData,'province');
        //$provinceList = array_count_values($provinceList);
        $idCardList = array_column($visitorInfoData,'id_card');
        foreach ($idCardList as $k2=>$v2)
        {
            $idCardList[$k2] = substr($v2,0,2);
        }
        $idCardList = array_count_values($idCardList);
        $provinceData = [
            'shanxi_visitor_count' => array_key_exists('61',$idCardList) ? $idCardList['61']: 0,
            'beijing_visitor_count'=> array_key_exists('11',$idCardList) ? $idCardList['11']: 0,
            'tianjin_visitor_count'=> array_key_exists('12',$idCardList) ? $idCardList['12']: 0,
            'hebei_visitor_count'=> array_key_exists('13',$idCardList) ? $idCardList['13']: 0,
            'sanxi_visitor_count'=> array_key_exists('14',$idCardList) ? $idCardList['14']: 0,
            'neimeng_visitor_count'=> array_key_exists('15',$idCardList) ? $idCardList['15']: 0,
            'liaoning_visitor_count'=> array_key_exists('21',$idCardList) ? $idCardList['21']: 0,
            'jilin_visitor_count'=> array_key_exists('22',$idCardList) ? $idCardList['22']: 0,
            'heilongjiang_visitor_count'=> array_key_exists('23',$idCardList) ? $idCardList['23']: 0,
            'shanghai_visitor_count'=> array_key_exists('31',$idCardList) ? $idCardList['31']: 0,
            'jiangsu_visitor_count'=> array_key_exists('32',$idCardList) ? $idCardList['32']: 0,
            'zhejiang_visitor_count'=> array_key_exists('33',$idCardList) ? $idCardList['33']: 0,
            'anhui_visitor_count'=> array_key_exists('34',$idCardList) ? $idCardList['34']: 0,
            'fujian_visitor_count'=> array_key_exists('35',$idCardList) ? $idCardList['35']: 0,
            'jiangxi_visitor_count'=> array_key_exists('36',$idCardList) ? $idCardList['36']: 0,
            'shandong_visitor_count'=> array_key_exists('37',$idCardList) ? $idCardList['37']: 0,
            'henan_visitor_count'=> array_key_exists('41',$idCardList) ? $idCardList['41']: 0,
            'hubei_visitor_count'=> array_key_exists('42',$idCardList) ? $idCardList['42']: 0,
            'hunan_visitor_count'=> array_key_exists('43',$idCardList) ? $idCardList['43']: 0,
            'guangdong_visitor_count'=> array_key_exists('44',$idCardList) ? $idCardList['44']: 0,
            'guangxi_visitor_count'=> array_key_exists('45',$idCardList) ? $idCardList['45']: 0,
            'hainan_visitor_count'=> array_key_exists('46',$idCardList) ? $idCardList['46']: 0,
            'chongqing_visitor_count'=> array_key_exists('50',$idCardList) ? $idCardList['50']: 0,
            'sichuan_visitor_count'=> array_key_exists('51',$idCardList) ? $idCardList['51']: 0,
            'guizhou_visitor_count'=> array_key_exists('52',$idCardList) ? $idCardList['52']: 0,
            'yunnan_visitor_count'=> array_key_exists('53',$idCardList) ? $idCardList['53']: 0,
            'xizang_visitor_count'=> array_key_exists('54',$idCardList) ? $idCardList['54']: 0,
            'gansu_visitor_count'=> array_key_exists('62',$idCardList) ? $idCardList['62']: 0,
            'qinghai_visitor_count'=> array_key_exists('63',$idCardList) ? $idCardList['63']: 0,
            'ningxia_visitor_count'=>array_key_exists('64',$idCardList) ? $idCardList['64']: 0,
            'xinjiang_visitor_count'=> array_key_exists('65',$idCardList) ? $idCardList['65']: 0,
            'xianggang_visitor_count'=> array_key_exists('81',$idCardList) ? $idCardList['81']: 0,
            'taiwan_visitor_count'=> array_key_exists('83',$idCardList) ? $idCardList['83']: 0,
            'aomen_visitor_count'=> array_key_exists('82',$idCardList) ? $idCardList['82']: 0
        ];

        return ['provinceData'=>$provinceData,'visitorAttData'=>$visitorAttData,'ids'=>$ids];
    }

    private static function getAge($birthday)
    {
        $date=date("Y-m-d");
        list($y,$m,$d)=explode("-",$birthday);
        list($xy,$xm,$xd)=explode("-", $date);

        $age=$xy-$y; // 当前年份减去客人出生年份
        if($xm>$m || $xm==$m&&$xd>$d) //判断月份和日期，如果当前日期大于客人出生       日期，年龄加一
        {
            $age=$age+1;
        }
        return $age;
    }

    public static function getTravelAgencyData()
    {
        $data = Db::Table(self::$table)
            ->Col(["travel_agency,count(id) as count"])
            ->GroupBy('travel_agency')
            ->OrderBy('count desc')
            ->Find();

        $totalCount = Db::Table(self::$table)
            ->Col(["count(id) as count"])
            ->Find()[0]['count'];

        foreach ($data as $k => $v)
        {
            $data[$k]['rate'] = round($v['count']/$totalCount,3);
        }
        return $data;
    }

    //TODO 省份组合查询
    public static function getDateVisitorData($province)
    {
        $date = date('Y-m-d');
        $province  = "('".implode("','",$province)."')";
        $travelAgencyData = Db::Table(self::$table)
            ->Col(['travel_agency,count(id) as count'])
            ->Where("province in {$province} AND date = '{$date}'")
            ->GroupBy('travel_agency')
            ->OrderBy('count desc')
            ->Limit(0,3)
            ->Find();

        $visitorWomenCount = Db::Table(self::$table)
            ->Col(['count(id) as count'])
            ->Where("province in {$province} AND date = '{$date}' AND sex = '女'")
            ->Find()[0]['count'];
        $visitorManCount = Db::Table(self::$table)
            ->Col(['count(id) as count'])
            ->Where("province in {$province} AND date = '{$date}' AND sex = '男'")
            ->Find()[0]['count'];
        $total_count = $visitorWomenCount + $visitorManCount;
        $visitorSexData = [
            'woman'=>['count'=>$visitorWomenCount,'rate'=>$total_count ? round($visitorWomenCount/$total_count,3): 0],
            'man'=>['count'=>$visitorManCount,'rate'=>$total_count ? round($visitorManCount/$total_count,3):0]
        ];

        $birthdays = Db::Table(self::$table)
            ->Col(['birthday'])
            ->Where("province in {$province} AND date = '{$date}'")
            ->Find();

        $birthday = array_column($birthdays,'birthday');
        $oldAgeCount = 0;
        $youngAgeCount = 0;

        foreach ($birthday as $k=>$v)
        {
            $age = self::getAge($v);
            if($age > 60){
                $oldAgeCount ++;
            }else{
                $youngAgeCount ++;
            }
        }
        $totalAgeCount = $oldAgeCount+$youngAgeCount;
        $visitorAgeData = $totalAgeCount ? [
            'oldAgeCount'=>$oldAgeCount,
            'youngAgeCount'=>$youngAgeCount,
            'oldAgeRate' => round($oldAgeCount/$totalAgeCount,3),
            'youngAgeRate' => round($youngAgeCount/$totalAgeCount,3)
        ] :
            [
                'oldAgeCount'=>0,
                'youngAgeCount'=>0,
                'oldAgeRate' => 0,
                'youngAgeRate' => 0
            ];
        return ['travelAgencyData'=>$travelAgencyData,'visitorSexData'=>$visitorSexData,
            'visitorAgeData'=>$visitorAgeData];
    }
    
    public static function getVisitorDataByTime($province,$startTime,$endTime)
    {
        $province  = "('".implode("','",$province)."')";
        $travelAgencyData = Db::Table(self::$table)
            ->Col(['travel_agency,count(id) as count'])
            ->Where("province in {$province} AND date between '{$startTime}' and '{$endTime}'")
            ->GroupBy('travel_agency')
            ->OrderBy('count desc')
            ->Limit(0,3)
            ->Find();

        $visitorWomenCount = Db::Table(self::$table)
            ->Col(['count(id) as count'])
            ->Where("province in {$province} AND date between '{$startTime}' and '{$endTime}' AND sex = '女'")
            ->Find()[0]['count'];

        $visitorManCount = Db::Table(self::$table)
            ->Col(['count(id) as count'])
            ->Where("province in {$province} AND date between '{$startTime}' and '{$endTime}' AND sex = '男'")
            ->Find()[0]['count'];

        $total_count = $visitorWomenCount + $visitorManCount;
        $visitorSexData = [
            'woman'=>['count'=>$visitorWomenCount,'rate'=>$total_count ? round($visitorWomenCount/$total_count,3): 0],
            'man'=>['count'=>$visitorManCount,'rate'=>$total_count ? round($visitorManCount/$total_count,3):0]
        ];

        $birthdays = Db::Table(self::$table)
            ->Col(['birthday'])
            ->Where("province in {$province} AND date between '{$startTime}' and '{$endTime}'")
            ->Find();

        $birthday = array_column($birthdays,'birthday');
        $oldAgeCount = 0;
        $youngAgeCount = 0;

        foreach ($birthday as $k=>$v)
        {
            $age = self::getAge($v);
            if($age > 60){
                $oldAgeCount ++;
            }else{
                $youngAgeCount ++;
            }
        }
        $totalAgeCount = $oldAgeCount+$youngAgeCount;
        $visitorAgeData = $totalAgeCount ? [
            'oldAgeCount'=>$oldAgeCount,
            'youngAgeCount'=>$youngAgeCount,
            'oldAgeRate' => round($oldAgeCount/$totalAgeCount,3),
            'youngAgeRate' => round($youngAgeCount/$totalAgeCount,3)
        ] :
        [
            'oldAgeCount'=>0,
            'youngAgeCount'=>0,
            'oldAgeRate' => 0,
            'youngAgeRate' => 0
        ];
        return ['travelAgencyData'=>$travelAgencyData,'visitorSexData'=>$visitorSexData,'visitorAgeData'=>$visitorAgeData];
    }

    public static function getDiffData($province,$startTime,$endTime,$startTimeSecond,$endTimeSecond)
    {
        $firstData = self::getVisitorDataByTime($province,$startTime,$endTime);
        $secondData = self::getVisitorDataByTime($province,$startTimeSecond,$endTimeSecond);
        $diffTime = strtotime($startTime)-strtotime($startTimeSecond);
//        var_dump($diffTime);die;
        if($diffTime > 0){
            $womanCountChange =
                $firstData['visitorSexData']['woman']['count']-$secondData['visitorSexData']['woman']['count'];
            $womanRateChange =
                $secondData['visitorSexData']['woman']['count'] ?
                    round($womanCountChange/$secondData['visitorSexData']['woman']['count'],3) : '--';

            $manCountChange =
                $firstData['visitorSexData']['man']['count']-$secondData['visitorSexData']['man']['count'];
            $manRateChange =
                $secondData['visitorSexData']['man']['count'] ?
                    round($manCountChange/$secondData['visitorSexData']['man']['count'],3) : '--';

            $oldAgeCount = $firstData['visitorAgeData']['oldAgeCount']-$secondData['visitorAgeData']['oldAgeCount'];
            $oldAgeRateChange = $secondData['visitorAgeData']['oldAgeCount'] ?
                round($oldAgeCount/$secondData['visitorAgeData']['oldAgeCount'],3):'--';

            $youngAgeCount = $firstData['visitorAgeData']['youngAgeCount']-$secondData['visitorAgeData']['youngAgeCount'];
            $youngAgeRateChange = $secondData['visitorAgeData']['youngAgeCount'] ?
                round($youngAgeCount/$secondData['visitorAgeData']['youngAgeCount'],3):'--';

        }
        else
        {
            $womanCountChange =
                $secondData['visitorSexData']['woman']['count']-$firstData['visitorSexData']['woman']['count'];
            $womanRateChange =
                $firstData['visitorSexData']['woman']['count'] ?
                    round($womanCountChange/$firstData['visitorSexData']['woman']['count'],3) : '--';

            $manCountChange =
                $secondData['visitorSexData']['man']['count']-$firstData['visitorSexData']['man']['count'];
            $manRateChange =
                $firstData['visitorSexData']['man']['count'] ?
                    round($manCountChange/$firstData['visitorSexData']['man']['count'],3) : '--';

            $oldAgeCount = $secondData['visitorAgeData']['oldAgeCount']-$firstData['visitorAgeData']['oldAgeCount'];
            $oldAgeRateChange = $firstData['visitorAgeData']['oldAgeCount'] ?
                round($oldAgeCount/$firstData['visitorAgeData']['oldAgeCount'],3):'--';

            $youngAgeCount = $secondData['visitorAgeData']['youngAgeCount']-$firstData['visitorAgeData']['youngAgeCount'];
            $youngAgeRateChange = $firstData['visitorAgeData']['youngAgeCount'] ?
                round($youngAgeCount/$firstData['visitorAgeData']['youngAgeCount'],3):'--';
        }
        $rateChange = [
            'womanRateChange'=>$womanRateChange,'manRateChange'=>$manRateChange,
            'oldAgeRateChange'=>$oldAgeRateChange,'youngAgeRateChange'=>$youngAgeRateChange
        ];
        $data = ['firstData'=>$firstData,'secondData'=>$secondData,'rateChange'=>$rateChange];
        return $data;
    }
}