<?php
/**
 * By yubin at 2018/12/11 4:13 PM.
 */

class Unique
{

    public static function Generate() : string
    {
        $serverId  = Yaf_Registry::get(COMMON)->serverId;
        $processId = getmypid();
        if( $processId===false )
        {
            $processId = mt_rand(10000,99999);
        }
        $processId = str_pad((string)$processId,5,0);
        $microTime = explode(' ', (string)microtime());
        $timestamp = $microTime[1].intval($microTime[0]*10000000);
        $random    = mt_rand(10,99);
        return "{$timestamp}{$random}{$serverId}{$processId}";
    }

    //签名验证函数
    public static function Sign($params)
    {
        Log::Debug(
            "check sign payData:{data} ",
            [
                '{data}' => json_encode($params,JSON_UNESCAPED_UNICODE)
            ],
            LOG_ALI_NOTIFY
        );
        $secret = Yaf_Registry::get(COMMON)->secret;
        ksort($params);
        $text = json_encode($params);
        $text = base64_encode($text);
        return md5($text . $secret);
    }
}