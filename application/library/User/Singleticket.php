<?php
namespace User;

/**
 * Class Singleticket
 * @package Singleticket
 */
class Singleticket
{

    /**
     * @param array $condition
     */
    public static function ResponseList(array $condition)
    {
        $page = \Page::Info();
        $list  = \SingleticketModel::List($condition, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        $count = \SingleticketModel::Count($condition);

        \Response::Json([
            'list'        => $list,
            'pageNo'      => $page['pageNo'],
            'pageCount'   => ceil($count/$page['pageSize']),
            'pageSize'    => $page['pageSize'],
            'recordCount' => $count
        ],'success');
    }

}