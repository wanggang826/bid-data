<?php

/**
 * Created by PhpStorm.
 * User: panxiongfei
 * Date: 2018/11/10
 * Time: 15:18
 */
class Util_String
{

    public static function getImageUrl($path)
    {
        if (empty($path)) {
            return $path;
        }
        $path = '/static/' . $path;
        return Url::getHttpUrl($path);
    }

    public static function mb_substr_replace($string, $replacement = '*', $start, $length = null)
    {
        $str_len = mb_strlen($string, 'UTF-8');
        if (empty($str_len)) {
            return $string;
        }

        $start_pos = $start;
        if ($start_pos < 0) {
            $start_pos = $start_pos + $str_len;
        }

        if ($length === null) {
            $end_pos = $str_len;
        } else if ($length < 0) {
            $end_pos = $str_len + $length;
        } else {
            $end_pos = $start_pos + $length;
        }

        $replacement_len = $end_pos - $start_pos;
        if ($start_pos >= $str_len || $end_pos <= 0 || $replacement_len <= 0) {
            return $string;
        }

        $head = mb_substr($string, 0, $start, 'UTF-8');
        $tail = mb_substr($string, $end_pos, $str_len - $end_pos, 'UTF-8');
        return $head . str_repeat($replacement, $replacement_len) . $tail;
    }

    public static function uuid()
    {
        $chars    = md5(microtime() . uniqid(mt_rand(), true));
        $segments = [
            substr($chars, 0, 8),
            substr($chars, 8, 4),
            substr($chars, 12, 4),
            substr($chars, 16, 4),
            substr($chars, 20, 12),
        ];

        return implode('-', $segments);
    }

    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    public static function getMd5($string)
    {
        $key = md5($string);
        return substr($key, 24, 8) . substr($key, 8, 16) . substr($key, 0, 8);
    }

    public static function str2hex($string)
    {
        $result  = bin2hex($string);
        $temp    = substr($result, -1, 1);
        $postfix = '';
        for ($i = 0; $i < $temp; $i++) {
            $postfix .= $temp;
        }
        return base64_encode($result . $postfix);
    }

    public static function hex2str($string)
    {
        $string  = base64_decode($string);
        $temp    = substr($string, -1, 1);
        $length  = strlen($string);
        $strTemp = substr($string, 0, $length - $temp);
        return hex2bin($strTemp);
    }

    public static function getUniqId()
    {
        $ymd              = date('Ymd', time());
        $his              = date('His', time());
        list($sec, $usec) = explode(".", microtime(true));
        $rand             = rand(100000, 999999);
        return $ymd . $usec . $his . $rand;
    }

    public static function createCoreSign($data)
    {
        ksort($data);
        $param = '';
        foreach ($data as $key => $val) {
            $param .= '&' . $key . '=' . $val;
        }
        $pre_sign = substr($param, 1);
        Log::Debug(
            "print pre_sign:{pre_sign} ",
            [
                '{pre_sign}'  => $pre_sign
            ],
            LOG_ALI_NOTIFY
        );
        return sha1(md5($pre_sign));
    }

    /**
     * @des 根据手机号查询归属地信息
     * @return Array
    (
    [types] => 中国联通
    [lng] => 108.940174
    [city] => 西安
    [num] => 1322788
    [isp] => 联通
    [area_code] => 610100
    [city_code] => 029
    [prov] => 陕西
    [zip_code] => 710000
    [lat] => 34.341568
    )
     */
    public static function getAddrFromMobile($mobile)
    {
        $host        = "https://api04.aliyun.venuscn.com/mobile";
        $requestData = ['mobile' => $mobile];
        $appCode     = "a7697328064448d2b2a6d4d98e1e4825"; //阿里云appcode配置
        $headers     = [];
        array_push($headers, "Authorization:APPCODE " . $appCode);

        $obj = new Curl($host);
        $obj->Header($headers);
        $obj->setShowReturnHeader();
        $result = $obj->Get($requestData);
        return $result['data']['data'];
    }

}
