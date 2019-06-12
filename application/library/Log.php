<?php

/**
 * Class Log
 */
class Log
{

    /**
     * 初始化日志
     */
    public static function Init()
    {
        $cfg = Yaf_Registry::get(CFG)->log;
        SeasLog::setBasePath($cfg->get('path'));
    }

    /**
     * @param string $info
     */
    public static function Info(string $info)
    {
        SeasLog::info( $info);
    }

    /**
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function Error(string $message, array $content = array(), $module = '')
    {
        SeasLog::error($message,  $content, $module );
    }

    /**
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function Notice(string $message, array $content = array(), $module = '')
    {
        SeasLog::notice($message,  $content, $module);
    }

    /**
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function Warning(string $message, array $content = array() , $module = '')
    {
        SeasLog::warning($message,  $content, $module);
    }

    /**
     * @param string $message
     * @param array $content
     * @param string $module
     */
    public static function Debug(string $message, array $content = array(),  $module = '')
    {
        SeasLog::debug($message,  $content, $module);
    }

    /**
     * @return string
     */
    public static function RequestId() : string
    {
        return SeasLog::getRequestID();
    }

}