<?php

namespace User;
/**
 * User: wgg
 * Date: 19-3-27
 * Time: 下午5:18
 */
class Line
{

    public static function ResponseList($condition,$frontList=false)
    {
        if($frontList)
        {
            $page = [
                'pageNo'   => (int)\Request::Param('pageNo'),
                'pageSize' => (int)\Request::Param('pageSize')
            ];
        }
        else
        {
            $page = [
                'pageNo'   => (int)\Request::Get('pageNo'),
                'pageSize' => (int)\Request::Get('pageSize')
            ];
        }

        if( $page['pageNo']<=0 )
        {
            $page['pageNo'] = 1;
        }

        if( $page['pageSize']<=0 || $page['pageSize']>500000 )
        {
            $page['pageSize'] = 10;
        }

        $page['startNum'] = ($page['pageNo']-1)*$page['pageSize'];

        $list = \LineModel::List($condition, $page['startNum'], $page['pageSize']);
        if (!$list) {
            $list = [];
            $count = 0;
            goto _RETURN;
        }

        $categoryIdArr = \DunHuangArray::getCols($list, 'id');
        $thumbnail = Attachment::GetThumbnail(Attachment::$BelongCategoryNo, $categoryIdArr);
        if(!empty($thumbnail))
        {
            $thumbnail = \DunHuangArray::toHash($thumbnail, 'belong_id', 'url');
        }
        $salePrice = \CategoryModel::getCategoryMinPrice($categoryIdArr);
        if (!empty($salePrice)) {
            $salePrice = \DunHuangArray::toHash($salePrice, 'id', 'salePrice');
        }
        foreach ($list as $key => $item) {
            $list[$key]['price'] = isset($salePrice[$item['id']])?$salePrice[$item['id']] . '起':"暂无";
            $list[$key]['img'] = isset($thumbnail[$item['id']])?$thumbnail[$item['id']]:"";
            $list[$key]['tags'] = explode(",",$item['remark']);
        }
        $count = \LineModel::Count($condition);
        _RETURN:
        
        \Response::Json([
            'list' => $list,
            'pageNo' => $page['pageNo'],
            'pageCount' => ceil($count / $page['pageSize']),
            'pageSize' => $page['pageSize'],
            'recordCount' => $count
        ], 'success');
    }

}