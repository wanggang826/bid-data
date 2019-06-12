<?php

namespace User;

/**
 * @brief 包车公共库
 *
 * @author Hu Zhangzheng
 * @version id: 1.0 2019/3/28 9:45
 */
class Carrent
{
    /**
     * @brief 包车产品列表
     *
     * @author Hu zhangzheng
     * @created 2019/3/29 10:36
     * @param $condition
     */
    public static function ResponseList($condition, $frontList = false)
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

        $list = \CarrentModel::List($condition, $page['startNum'], $page['pageSize']);
        if (!$list) {
            $list = [];
            $count = 0;
            goto _RETURN;
        }

        $categoryIdArr = \DunHuangArray::getCols($list, 'id');

        if($frontList)
        {
            //获取缩略图
            $thumbnail = Attachment::GetThumbnail(Attachment::$BelongCategoryNo, $categoryIdArr);
            if (!empty($thumbnail)) {
                $thumbnail = \DunHuangArray::toHash($thumbnail, 'belong_id', 'url');
            }

        }
        $salePrice = \CategoryModel::getCategoryMinPrice($categoryIdArr);
        if (!empty($salePrice)) {
            $salePrice = \DunHuangArray::toHash($salePrice, 'id', 'salePrice');
        }

        foreach ($list as $key => $item) {
            if (isset($salePrice[$item['id']])) {
                $list[$key]['price'] = $salePrice[$item['id']] . '起';
            } else {
                $list[$key]['price'] = '暂无';
            }
            if($frontList){
                if (isset($thumbnail[$item['id']])) {
                    $list[$key]['img'] = $thumbnail[$item['id']];
                } else {
                    $list[$key]['img'] = '';
                }
            }
            $list[$key]['tags'] = explode(",",$item['tags']);
        }

        $count = \CarrentModel::Count($condition);
        _RETURN:

        \Response::Json([
            'list' => $list,
            'pageNo' => $page['pageNo'],
            'pageCount' => ceil($count / $page['pageSize']),
            'pageSize' => $page['pageSize'],
            'recordCount' => $count
        ], 'success');
    }

    /**
     * @brief 车型列表
     *
     * @author Hu zhangzheng
     * @created 2019/3/29 10:36
     * @param $condition
     */
    public static function ResponseCartypeList($condition)
    {
        $page = \Page::Info();
        $list = \CartypeModel::List($condition, $page['startNum'], $page['pageSize']);
        if (!$list) {
            $list = [];
        }

        $count = \CartypeModel::Count($condition);

        \Response::Json([
            'list' => $list,
            'pageNo' => $page['pageNo'],
            'pageCount' => ceil($count / $page['pageSize']),
            'pageSize' => $page['pageSize'],
            'recordCount' => $count
        ], 'success');
    }
}