<?php


/**
 * Class Page
 */
class Page
{
    /**
     * @return array
     */
    public static function Info() : array
    {
        $page = [
            'pageNo'   => (int)\Request::Get('pageNo'),
            'pageSize' => (int)\Request::Get('pageSize')
        ];

        if( $page['pageNo']<=0 )
        {
            $page['pageNo'] = 1;
        }

        if( $page['pageSize']<=0 || $page['pageSize']>500000 )
        {
            $page['pageSize'] = 10;
        }

        $page['startNum'] = ($page['pageNo']-1)*$page['pageSize'];
        return $page;
    }

}