<?php

class Conf {

    static public function getConf($item, $onlyApp = false) {
        if (empty($item)) {

            return false;
        }

        $query = explode('/', $item);

        $path = '';
        $filePath = '';
        $index = $query;
        foreach ($query as $k => $v) {

            array_shift($index);
            $loadPath = APPLICATION_PATH . '/conf' . $path . '/' . $v;
            if (is_dir($loadPath)) {

                $path .= '/' . $v;
                continue;
            }

            if (is_file($loadPath . '.ini')) {

                $filePath = $loadPath . '.ini';
                break;
            }

            break;
        }

        if (!$onlyApp && !$filePath) {

            $index = $query;
            foreach ($query as $k => $v) {

                array_shift($index);
                $loadPath = APPLICATION_PATH . '/conf' . $path . '/' . $v;
                if (is_dir($loadPath)) {

                    $path .= '/' . $v;
                    continue;
                }

                if (is_file($loadPath . '.ini')) {

                    $filePath = $loadPath . '.ini';
                    break;
                }

                break;
            }
        }

        if (!$filePath) {

            return NULL;
        }

        $config = new Yaf_Config_Ini($filePath);
        $config = $config->toArray();


        if (empty($index)) {

            return $config;
        }

        foreach ($index as $k => $v) {

            if (isset($config[$v])) {

                $config = $config[$v];
            } else {

                return NULL;
            }
        }

        return $config;
    }

    static public function getAppConf($item) {

        self::getConf($item, true);
    }

}
