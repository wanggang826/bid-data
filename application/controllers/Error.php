<?php

class ErrorController extends Yaf_Controller_Abstract {

	public function errorAction($exception)
    {
		 Log::Error(
		    "Yaf Error Msg: {msg}",
            [
                '{msg}' => $exception->getMessage()
            ]
        );

		 Response::Error($exception->getMessage(),-1);
	}

}
