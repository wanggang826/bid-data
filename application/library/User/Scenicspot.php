<?php
namespace User;

/**
 * Class Scenicspot
 * @package Scenicspot
 */
class Scenicspot
{

    /**
     * @param string $where
     */
    public static function ResponseList(string $where)
    {
        $page = \Page::Info();
        $list  = \ScenicspotModel::List($where, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        $count = \ScenicspotModel::Count($where);

        \Response::Json([
            'list'        => $list,
            'pageNo'      => $page['pageNo'],
            'pageCount'   => ceil($count/$page['pageSize']),
            'pageSize'    => $page['pageSize'],
            'recordCount' => $count
        ],'success');
    }

}