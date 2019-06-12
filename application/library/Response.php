<?php

use \Xml\Writer;

/**
 * Class Response
 */
class Response
{
    /**
     * @var array
     */
    public static $header = [
        'json' => 'Content-type:text/json',
        'xml'  => 'Content-type: text/xml',
    ];

    /**
     * @var int
     */
    public static $tokenError = 401;

    /**
     * @var int
     */
    public static $noPermission = 405;


    /**
     * @var int
     */
    public static $BeingForbidden = 403;

    /**
     * 设置返回文件类型头
     * @param string $type
     */
    private static function header($type = 'json')
    {
        header(static::$header[$type]);
    }

    /**
     * Json 输出json格式数据
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    public static function Json(array $data, string $msg = '', int $code = 200)
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        self::Output($response);
    }

    /**
     * Error 输出json格式错误
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    public static function Error(string $msg, int $code = -1, $data = [])
    {
        $response = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        self::Output($response);
    }

    /**
     * @param array $response
     */
    public static function Output(array $response)
    {
        static::header();
        
        $trace = debug_backtrace();
        Log::Debug(
            'Uri:[{uri}] | Refer:[{refer}] | File:[{file}] | Line:[{line}] | Response:[{response}]',
            [
                '{uri}'    => $_SERVER['REQUEST_URI'],
                '{refer}'  => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                '{file}'   => basename($trace[1]['file']),
                '{line}'   => $trace[1]['line'],
                '{response}'   => json_encode($response, JSON_UNESCAPED_UNICODE),
            ],
            LOG_REQUEST_DIR
        );
        
        exit(json_encode($response,JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $string
     */
    public static function String(string $string)
    {
        Log::Debug(
            'Response:{response}',
            [
                '{response}' => $string,
            ],
            LOG_REQUEST_DIR
        );
        exit($string);
    }

    /**
     * 输出xml格式数据
     * @param array $data
     * @return bool
     */
    public static function Xml(array $data): bool
    {
        static::header('xml');
        $writer = new Writer('1.0', 'UTF-8');
        exit($writer->makeFromArray($data)->getXml());
    }

}
