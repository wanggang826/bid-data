<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{

    private $cfgPath = APPLICATION_PATH . '/conf/';

    private $cfgExt = '.ini';

    public function _initConfig()
    {
        //load config
        Yaf_Registry::set(CFG, new Yaf_Config_Ini($this->cfgPath . APPLICATION_NAME . $this->cfgExt, ENV));
        //load permission
        Yaf_Registry::set(COMMON, new Yaf_Config_Ini($this->cfgPath . COMMON . $this->cfgExt));
        //load third partner config
//        Yaf_Registry::set(CFG_PARTNER, new Yaf_Config_Ini($this->cfgPath . CFG_PARTNER . $this->cfgExt, ENV));
        //关闭自动渲染
//        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {

    }

    public function _initLog(Yaf_Dispatcher $dispatcher)
    {
        Log::Init();
    }

    public function _initCommonFunctions()
    {
        Yaf_Loader::import(Yaf_Application::app()->getConfig()->application->directory . '/common/functions.php');
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->getRouter()->addRoute('admin', new Yaf_Route_Rewrite(
            '/login',
            [
                'controller' => 'Index',
                'action' => 'login',
            ]
        ));

        $dispatcher->getRouter()->addRoute('submitData', new Yaf_Route_Rewrite(
            '/submit_data',
            [
                'controller' => 'Index',
                'action' => 'formData',
            ]
        ));

        $dispatcher->getRouter()->addRoute('api', new Yaf_Route_Rewrite(
            '/api',
            [
                'controller' => 'Api',
                'action' => 'getData',
            ]
        ));

        $dispatcher->getRouter()->addRoute('idCardExcel', new Yaf_Route_Rewrite(
            '/id_card_excel',
            [
                'controller' => 'Index',
                'action' => 'idCardExcel',
            ]
        ));

        $dispatcher->getRouter()->addRoute('dxgCarExcel', new Yaf_Route_Rewrite(
            '/dxg_car_excel',
            [
                'controller' => 'Index',
                'action' => 'dxgCarExcel',
            ]
        ));

        $dispatcher->getRouter()->addRoute('hhlCarExcel', new Yaf_Route_Rewrite(
            '/hhl_car_excel',
            [
                'controller' => 'Index',
                'action' => 'hhlCarExcel',
            ]
        ));

        $dispatcher->getRouter()->addRoute('hhlCarExcel', new Yaf_Route_Rewrite(
            '/reception_num_excel',
            [
                'controller' => 'Index',
                'action' => 'receptionNumExcel',
            ]
        ));

        $dispatcher->getRouter()->addRoute('login', new Yaf_Route_Rewrite(
            '/admin/:password',
            [
                'controller' => 'Index',
                'action' => 'index',
            ]
        ));

        $dispatcher->getRouter()->addRoute('getReceptionNum', new Yaf_Route_Rewrite(
            '/get_reception_num',
            [
                'controller' => 'Api',
                'action' => 'getReceptionNum',
            ]
        ));

        $dispatcher->getRouter()->addRoute('getHotelData', new Yaf_Route_Rewrite(
            '/get_hotel_data',
            [
                'controller' => 'Api',
                'action' => 'getHotelData',
            ]
        ));

        $dispatcher->getRouter()->addRoute('getParkingData', new Yaf_Route_Rewrite(
            '/get_parking_data',
            [
                'controller' => 'Api',
                'action' => 'getParkingData',
            ]
        ));
        
        $dispatcher->getRouter()->addRoute('getSingleGroup', new Yaf_Route_Rewrite(
            '/get_single_group',
            [
                'controller' => 'Api',
                'action' => 'getSingleGroup',
            ]
        ));
        
        $dispatcher->getRouter()->addRoute('getVisitorData', new Yaf_Route_Rewrite(
            '/get_visitor_data',
            [
                'controller' => 'Api',
                'action' => 'getVisitorData',
            ]
        ));


    }


}
