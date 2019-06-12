<?php
namespace User;

/**
 * Class Room
 * @package Room
 */
class Room
{

    public static function ResponseList($condition)
    {
        $page = \Page::Info();
        $list  = \RoomModel::List($condition, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        $count = \RoomModel::Count($condition);

        \Response::Json([
            'list'        => $list,
            'pageNo'      => $page['pageNo'],
            'pageCount'   => ceil($count/$page['pageSize']),
            'pageSize'    => $page['pageSize'],
            'recordCount' => $count
        ],'success');
    }

}