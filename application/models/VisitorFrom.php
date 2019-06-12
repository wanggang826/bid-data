<?php
/**
 * User: wgg
 * Date: 19-4-12
 * Time: 下午2:43
 */
class VisitorFromModel
{
    private static $table = 'visitor_from';

    public static function Insert($data)
    {
        $result = Db::Table(self::$table)->Insert($data);
        if ($result['result']) {
            return $result['insertId'];
        }
        return false;
    }
    

    public static function getVisitorFromSortData()
    {
        $data = Db::Table(static::$table)
            ->Col(["sum(shanxi_visitor_count) as 陕西,
                sum(beijing_visitor_count) as 北京,
                sum(tianjin_visitor_count) as 天津,
                sum(hebei_visitor_count) as 河北,
                sum(sanxi_visitor_count) as 山西,
                sum(neimeng_visitor_count) as 内蒙古,
                sum(liaoning_visitor_count) as 辽宁,
                sum(jilin_visitor_count) as 吉林,
                sum(heilongjiang_visitor_count) as 黑龙江,
                sum(shanghai_visitor_count) as 上海,
                sum(jiangsu_visitor_count) as 江苏,
                sum(zhejiang_visitor_count) as 浙江,
                sum(anhui_visitor_count) as 安徽,
                sum(fujian_visitor_count) as 福建,
                sum(jiangxi_visitor_count) as 江西,
                sum(shandong_visitor_count) as 山东,
                sum(henan_visitor_count) as 河南,
                sum(hubei_visitor_count) as 湖北,
                sum(hunan_visitor_count) as 湖南,
                sum(guangdong_visitor_count) as 广东,
                sum(guangxi_visitor_count) as 广西,
                sum(hainan_visitor_count) as 海南,
                sum(chongqing_visitor_count) as 重庆,
                sum(sichuan_visitor_count) as 四川,
                sum(guizhou_visitor_count) as 贵州,
                sum(yunnan_visitor_count) as 云南,
                sum(xizang_visitor_count) as 西藏,
                sum(gansu_visitor_count) as 甘肃,
                sum(qinghai_visitor_count) as 青海,
                sum(ningxia_visitor_count) as 宁夏,
                sum(xinjiang_visitor_count) as 新疆,
                sum(xianggang_visitor_count) as 香港,
                sum(taiwan_visitor_count) as 台湾,
                sum(aomen_visitor_count) as 澳门 "])
            ->Find()[0];
        arsort($data);
        $outData = [];
        foreach ($data as $k=>$v)
        {
            $outData[$k]['jd']    = getJdWd($k)['jd'];
            $outData[$k]['wd']    = getJdWd($k)['wd'];
            $outData[$k]['place'] = $k;
            $outData[$k]['num']   = $v;
            $outData[$k]['goalcity'] = '青铜峡';
            $outData[$k]['goaljd']   = '106.07';
            $outData[$k]['goalwd']   = '38.02';
        }
        return array_values($outData);
    }

    public static function getJdWd($city)
    {
       $cityJdWdArray = [
           '安徽'    =>['wd'=>'31.833227236' ,'jd'=>'117.225382556'],
           '北京'    =>['wd'=>'39.907582828','jd'=>'116.396278113'],
           '重庆'    =>['wd'=>'29.584243617','jd'=>'106.552742562'],
           '福建'    =>['wd'=>'26.087646551','jd'=>'119.294977006'],
           '甘肃'    =>['wd'=>'36.06862','jd'=>'103.834837015'],
           '广东'	=>['wd'=>'23.1337996','jd'=>'113.264003133'],
           '广西'	=>['wd'=>'22.822332115','jd'=>'108.367031241'],
           '贵州'	=>['wd'=>'26.651206357','jd'=>'106.629748777'],
           '海南'	=>['wd'=>'20.031296614','jd'=>'110.33519473'],
           '河北'	=>['wd'=>'38.052514721','jd'=>'114.510778463'],
           '河南'	=>['wd'=>'34.750117438','jd'=>'113.625821885'],
           '黑龙江'  =>['wd'=>'45.8068887155405','jd'=>'126.530422423016'],
           '湖北'    =>['wd'=>'30.602362201','jd'=>'114.303430004'],
           '湖南'     =>['wd'=>'28.242409436','jd'=>'112.933453806'],
           '吉林'    =>['wd'=>'43.8451438670654','jd'=>'125.352175361688'],
           '江苏'     =>['wd'=>'32.060487154','jd'=>'118.791055713'],
           '江西'     =>['wd'=>'28.697343237','jd'=>'115.855008674'],
           '辽宁'  	 =>['wd'=>'41.843668658','jd'=>'123.470066583'],
           '内蒙'    =>['wd'=>'40.854225087','jd'=>'111.747841898'],
           '宁夏'     =>['wd'=>'38.493732055','jd'=>'106.229494689'],
           '青海'    =>['wd'=>'36.637156772','jd'=>'101.785092875'],
           '山东' =>['wd'=>'36.674377704','jd'=>'116.991151021'],
           '山西'	=>['wd'=>'37.87601518','jd'=>'112.546635233'],
           '陕西'	=>['wd'=>'34.272091764','jd'=>'108.940092354'],
           '上海'	=>['wd'=>'31.237101598','jd'=>'121.472696621'],
           '四川'	=>['wd'=>'30.6672631','jd'=>'104.06518791'],
           '天津'	=>['wd'=>'39.089389984','jd'=>'117.195369004'],
           '西藏'		=>['wd'=>'29.654136405','jd'=>'91.11474124'],
           '新疆'	=>['wd'=>'43.834482828','jd'=>'87.615730096'],
           '云南'		=>['wd'=>'25.045835583','jd'=>'102.71890305'],
           '浙江'		=>['wd'=>'30.280161578','jd'=>'120.154995513'],
           '香港' =>['wd'=>'21.23','jd'=>'115.12'],
           '澳门'	=>['wd'=>'22.33','jd'=>'113.33'],
           '台湾'	=>['wd'=>'25.03','jd'=>'121.30']
        ];
        return $cityJdWdArray[$city];
    }
}