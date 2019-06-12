<?php
/**
 * @date    2018-11-19 11:50
 * @desc    yaf cli 模式调用入口
 * @eg     php cli.php module controller action param
 *                     模块    控制器      方法    参数
 */
global $argv;
$configNode = ( isset($argv[2]) && in_array($argv[2],['DEV','TEST','TRIAL','PRODUCTION']) ) ? $argv[2] : "DEV";

define('ENV', $configNode);
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
$application = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");


// php cli.php request_uri="/mobile_order/changecli"

$application->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());
exit();
?>
