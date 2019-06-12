<?php

/**
 * Class Controller
 */
class Controller extends Yaf_Controller_Abstract
{

    public function init()
    {
        Request::Init($this->getRequest());
    }

}
