<?php

/**
 * Class Request
 */
class Request
{

    /**
     * @var Yaf_Request_Abstract
     * */
    private static $request;

    /**
     * @var array
     */
    private static $_PUT = [];

    //日志格式示例
    //{dateTime} | {level} | {pid} | {uniqid} | {timeStamp} | {logInfo}

    /**
     * @param Yaf_Request_Abstract $request
     */
    public static function Init(Yaf_Request_Abstract $request)
    {
        static::$request = $request;
        static::_verifyMethod();
        static::_parseRaw();
    }

    private static function _parseRaw()
    {
        $raw = Request::Raw();
        //前端 通过post form-data传值
        if (empty($raw) || count($_POST) > 0)
        {
            goto SET_PUT;
        }

        //前端 通过传json字符串
        $jsonRaw = json_decode($raw, JSON_UNESCAPED_UNICODE);
        if (is_array($jsonRaw))
        {
            $_POST = $jsonRaw;
            goto SET_PUT;
        }

        //前端 通过 x-url-from-urlencode形式传值
        $params = explode('&', urldecode($raw));
        foreach ($params as $eachParam)
        {
            $param = explode('=', $eachParam);
            if (count($param) < 2)
            {
                continue;
            }
            $value = $param;
            unset($value[0]);
            static::_setInput($param[0], join('=', $value));
        }

        SET_PUT:
        static::$_PUT = $_POST;

        $trace = debug_backtrace();
        $httpCookie = !empty($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '';

        Log::Debug(
            'Uri:[{uri}] | Refer:[{refer}] | Cookie:[{cookie}] | File:[{file}] | Line:[{line}] | Post:[{post}] | Get:[{get}] | Put:[{put}] | Raw:[{raw}] | Method:[{method}]',
            [
                '{uri}'    => $_SERVER['REQUEST_URI'],
                '{refer}'  => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                '{cookie}' => $httpCookie,
                '{file}'   => basename($trace[1]['file']),
                '{line}'   => $trace[1]['line'],
                '{post}'   => json_encode($_POST, JSON_UNESCAPED_UNICODE),
                '{get}'    => json_encode($_GET, JSON_UNESCAPED_UNICODE),
                '{put}'    => json_encode(static::$_PUT, JSON_UNESCAPED_UNICODE),
                '{raw}'    => urldecode(Request::Raw()),
                '{method}' => Request::Method()
            ],
            LOG_REQUEST_DIR
        );
    }

    private static function _setInput(string $key, string $val)
    {
        if (strpos($key, '[]') === false) {
            $_POST[$key] = $val;
            return;
        }
        $key = str_replace('[]', '', $key);
        $_POST[$key][] = $val;
    }

    /**
     * verify whether the request method is support.
     */
    private static function _verifyMethod()
    {
        if (static::Method() == 'OPTIONS') {
            Response::Error('unsupported method.');
        }
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed
     */
    public static function Get(string $key, string $default = '')
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed
     */
    public static function Post(string $key, string $default = '')
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return $default;
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed|string
     */
    public static function Put(string $key, string $default = '')
    {
        if (isset(static::$_PUT[$key])) {
            return static::$_PUT[$key];
        }
        return $default;
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed
     */
    public static function Server(string $key, string $default = '')
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return $default;
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed
     */
    public static function Param(string $key, string $default = '')
    {
        return static::$request->getParam($key, $default);
    }

    /**
     * @param string      $key
     * @param string|null $default
     * @return mixed
     */
    public static function Cookie(string $key, string $default = '')
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return $default;
    }

    /**
     * @return string
     */
    public static function Method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return mixed
     */
    public static function Raw()
    {
        return file_get_contents('php://input');
    }

    /**
     * @return string
     */
    public static function Action(): string
    {
        return static::$request->action;
    }

    /**
     * @return string
     */
    public static function Controller(): string
    {
        return static::$request->controller;
    }

    /**
     * @return string
     */
    public static function Uri(): string
    {
        return static::$request->getRequestUri();
    }

}
