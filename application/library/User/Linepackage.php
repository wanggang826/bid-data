<?php
namespace User;
/**
 * User: wgg
 * Date: 19-3-28
 * Time: 上午11:41
 */
class Linepackage
{
    public static function ResponseList($condition)
    {
        $page = \Page::Info();
        $list  = \LinepackageModel::List($condition, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        $count = \LinepackageModel::Count($condition);

        \Response::Json([
            'list'        => $list,
            'pageNo'      => $page['pageNo'],
            'pageCount'   => ceil($count/$page['pageSize']),
            'pageSize'    => $page['pageSize'],
            'recordCount' => $count
        ],'success');
    }
}