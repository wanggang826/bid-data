<?php
/**
 * By yubin at 2019/1/16 2:10 PM.
 */

class Mobile
{

    private static $_url = 'https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=';

    public static function GetBelong(string $mobile): array
    {
        $result = (new Curl(static::$_url . $mobile))->Get([], 'string');
        if ($result['code'] != 200) {
            return [];
        }
        $resultUtf8 = iconv("gb2312", "utf-8", $result['data']);
        $resultArray = explode(PHP_EOL, $resultUtf8);
        if (!isset($resultArray[2])) {
            return [];
        }
        $data = [
            'province' => str_replace(['    province:\'', '\'', ',', ' '], '', $resultArray['2']),
            'isp' => ''
        ];
        if (!isset($resultArray[7])) {
            return $data;
        }
        $data['isp'] = str_replace(['	carrier:\'', '\'', ',', '	'], '', $resultArray[7]);
        return $data;

    }

    /**
     * @brief 计算地球两个经纬度之间的距离
     *
     * @author Hu zhangzheng
     * @created 2019/3/1 15:03
     * @param $lat1 纬度
     * @param $lng1 经度
     * @param $lat2
     * @param $lng2
     * @param $circle 允许的半径
     * @param float $radius 地球半径
     * @return float|int
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2, $circle, $radius = 6378.137)
    {
        //将角度转为狐度
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度

        $radLat2 = deg2rad($lat2);

        $radLng1 = deg2rad($lng1);

        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;

        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * $radius;

        if ($s > $circle) {
            return false;
        }
        return true;
    }

}