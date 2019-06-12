<?php
//环境：DEV开发环境   PRODUCTION生产环境
define('ENV', $_SERVER['RUNTIME']);
define('APPLICATION_PATH', dirname(__FILE__));
//应用名称，关系配置文件加载
define('APPLICATION_NAME', 'boms_api');
//应用名称，关系配置文件加载

define('CFG', 'cfg');
//公共配置文件名称
define('COMMON', 'common');


//=====日志模块名=====
define('LOG_ALI', APPLICATION_NAME);
define('LOG_WX', APPLICATION_NAME);
define('LOG_PARK', APPLICATION_NAME);
define('LOG_DB', APPLICATION_NAME);
define('LOG_WX_NOTIFY', APPLICATION_NAME);
define('LOG_ALI_NOTIFY', APPLICATION_NAME);
define('LOG_REQUEST_DIR','request');
if(ENV == 'DEV'|| ENV=='dev'){
    error_reporting(E_ALL & ~E_NOTICE);
}else{
    error_reporting(0);
}
ini_set('serialize_precision', 14);
$application = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
$application->bootstrap()->run();


