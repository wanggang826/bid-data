<?php
namespace User;
/**
 * Class Role
 */
/**
 * Class Role
 * @package Role
 */
class Role
{

    /**
     * @param array $condition
     */
    public static function ResponseList(array $condition)
    {
        $page = \Page::Info();
        $list  = \RoleModel::List($condition, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        $count = \RoleModel::Count($condition);

        \Response::Json([
            'list'      => $list,
            'pageNo'    => $page['pageNo'],
            'pageCount' => ceil($count/$page['pageSize']),
            'pageSize'  => $page['pageSize'],
            'recordCount' => $count
        ],'success');
    }

}