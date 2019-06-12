<?php
/**
 * Created by PhpStorm.
 * User: yubin
 * Date: 2018/10/25
 * Time: 6:22 PM
 */

namespace User;


class Attribute
{

    private static function _parseParameter(int $userId) : array
    {
        $parameter = [
            'name'   => \Request::Post('name'),
            'icon'   => (int)\Request::Post('icon'),
            'remark' => \Request::Post('remark'),
            'status' => 1,
            'manager_id' => $userId
        ];
        if( !\Verifier::IsString($parameter['name']) )
        {
            \Response::Error('名称格式不正确');
        }

        if( !empty($parameter['remark']) && !\Verifier::IsText($parameter['remark'],600) )
        {
            \Response::Error('说明格式不正确');
        }

        if( empty($parameter['remark']) )
        {
            unset($parameter['remark']);
        }

        if( $parameter['icon']<=0 )
        {
            unset($parameter['icon']);
        }

        return $parameter;
    }

    public static function Detail(int $type)
    {
        $detail = \AttributeModel::Detail(
            \Request::Param('id'),
            $type
        );

        if( !$detail )
        {
            \Response::Error('获取失败');
        }
        \Response::Json(['detail'=>$detail]);
    }

    public static function Insert(int $type, int $uid)
    {
        if( \AttributeModel::Insert(
            $type,
            static::_parseParameter( $uid )
        ) )
        {
            \Response::Json(['result'=>1]);
        }
        \Response::Error('新增失败');
    }

    public static function Update(int $type ,int $uid)
    {
        if( \AttributeModel::Update(
            (int)\Request::Param('id'),
            $type,
            static::_parseParameter( $uid )
        ) )
        {
            \Response::Json(['result'=>1]);
        }
        \Response::Error('更新失败');
    }

    public static function Delete(int $type ,int $uid)
    {
        if( \AttributeModel::Update(
            (int)\Request::Param('id'),
            $type,
            [
                'status'     => 0,
                'manager_id' => $uid
            ]
        ) )
        {
            \Response::Json(['result'=>1]);
        }
        \Response::Error('更新失败');
    }


    public static function List(int $type)
    {
        $condition = [];
        $name = \Verifier::ReplaceSpecialSymbol(\Request::Get('name'));
        if( $name != '' )
        {
            $condition['name'] = $name;
        }

        $page = \Page::Info();

        $count = \AttributeModel::Count($type, $condition);
        $list  = \AttributeModel::List($type, $condition, $page['startNum'], $page['pageSize']);
        if( !$list )
        {
            $list = [];
        }

        \Response::Json([
            'list'      => $list,
            'pageNo'    => $page['pageNo'],
            'pageCount' => ceil($count/$page['pageSize']),
            'pageSize'  => $page['pageSize'],
            'recordCount' => $count
        ]);
    }

    public static function AuthorizedList(int $type)
    {
        $condition = [];
        $name = \Verifier::ReplaceSpecialSymbol(\Request::Get('name'));
        if( !empty($name) )
        {
            $condition['name'] = $name;
        }

        $page = \Page::Info();
        $page['pageSize'] = 500;

        $count = \AttributeModel::Count($type, $condition);
        $list  = \AttributeModel::List($type, $condition, $page['startNum'], $page['pageSize'], 1);
        if( !$list )
        {
            $list = [];
        }

        \Response::Json([
            'list'      => $list,
            'pageNo'    => $page['pageNo'],
            'pageCount' => ceil($count/$page['pageSize']),
            'pageSize'  => $page['pageSize'],
            'recordCount' => $count
        ]);
    }

}