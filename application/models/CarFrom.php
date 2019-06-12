<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:54
 */
class CarFromModel
{
    private static $table = 'car_from';

    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }

    public static function InsertBySql($sqlValues)
    {
        $sql = "insert into  `car_from`(
          `date`,`shanxi_car_count`,`beijing_car_count`,
          `tianjin_car_count`,`hebei_car_count`,`sanxi_car_count`,
          `neimeng_car_count`,`liaoning_car_count`,`jilin_car_count`,
          `heilongjiang_car_count`,`shanghai_car_count`,`jiangsu_car_count`,
          `zhejiang_car_count`,`anhui_car_count`,`fujian_car_count`,
          `jiangxi_car_count`,`shandong_car_count`,`henan_car_count`,
          `hubei_car_count`,`hunan_car_count`,`guangdong_car_count`,
          `guangxi_car_count`,`hainan_car_count`,`chongqing_car_count`,
          `sichuan_car_count`,`guizhou_car_count`,`yunnan_car_count`,
          `xizang_car_count`,`gansu_car_count`,`qinghai_car_count`,
          `ningxia_car_count`,`xinjiang_car_count`,`xianggang_car_count`,
          `taiwan_car_count`,`aomen_car_count`
          ) values ".$sqlValues;
        $result = Db::Table(self::$table)->executeSql($sql);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }
    
    public static function getCarFromSortData()
    {
        $beforeOneYear = date('Y',strtotime("-1 year"));
        $nowYear = date('Y');

        $carData = Db::Table(static::$table)
            ->Col(["year(date) as year,sum(shanxi_car_count) as 陕西,
                sum(beijing_car_count) as 北京,
                sum(tianjin_car_count) as 天津,
                sum(hebei_car_count) as 河北,
                sum(sanxi_car_count) as 山西,
                sum(neimeng_car_count) as 内蒙古,
                sum(liaoning_car_count) as 辽宁,
                sum(jilin_car_count) as 吉林,
                sum(heilongjiang_car_count) as 黑龙江,
                sum(shanghai_car_count) as 上海,
                sum(jiangsu_car_count) as 江苏,
                sum(zhejiang_car_count) as 浙江,
                sum(anhui_car_count) as 安徽,
                sum(fujian_car_count) as 福建,
                sum(jiangxi_car_count) as 江西,
                sum(shandong_car_count) as 山东,
                sum(henan_car_count) as 河南,
                sum(hubei_car_count) as 湖北,
                sum(hunan_car_count) as 湖南,
                sum(guangdong_car_count) as 广东,
                sum(guangxi_car_count) as 广西,
                sum(hainan_car_count) as 海南,
                sum(chongqing_car_count) as 重庆,
                sum(sichuan_car_count) as 四川,
                sum(guizhou_car_count) as 贵州,
                sum(yunnan_car_count) as 云南,
                sum(xizang_car_count) as 西藏,
                sum(gansu_car_count) as 甘肃,
                sum(qinghai_car_count) as 青海,
                sum(ningxia_car_count) as 宁夏,
                sum(xinjiang_car_count) as 新疆,
                sum(xianggang_car_count) as 香港,
                sum(taiwan_car_count) as 台湾,
                sum(aomen_car_count) as 澳门 "])
            ->GroupBy('year')
            ->Find();
        $data = [];
        foreach ($carData as $k=>$v){
            $value = $v;
            unset($value['year']);
            arsort($value);
            $data[$v['year']] = array_slice($value,0,10);
            unset($carData[$k]);
        }
        $beforeOneYearData = array_key_exists($beforeOneYear,$data) ? $data[$beforeOneYear] : [];
        $nowYearData = array_key_exists($nowYear,$data) ? $data[$nowYear] : [];
        $outData = [];
        $i = 0;
        $outData[$beforeOneYear] = [];$outData[$nowYear] = [];
        foreach ($beforeOneYearData as $k1=>$v1)
        {
            $total1 = array_sum($beforeOneYearData);
            $rate1  = $v1/$total1;
            $rate1 = round($rate1,3)*100;
            $outData[$beforeOneYear][$i]['name'] = $k1.$rate1.'%';
            $outData[$beforeOneYear][$i]['num'] = $v1;
            $outData[$beforeOneYear][$i]['rate'] = $rate1;
            $i++;
        }
        $j = 0;
        foreach ($nowYearData as $k2=>$v2)
        {
            $total2 = array_sum($nowYearData);
            $rate2  = $total2 ? $v2/$total2 : 0;
            $rate2 = round($rate2,3)*100;
            $outData[$nowYear][$j]['name'] = $k2.$rate2.'%';
            $outData[$nowYear][$j]['num'] = $v2;
            $outData[$nowYear][$j]['rate'] = $rate2;
            $j++;
        }
        return $outData;
    }
}
