<?php
/**
 * By yubin at 2018/11/21 9:29 AM.
 */

namespace User;

use OSS\Oss;

/**
 * Class Attachment
 * @package User
 */
class Attachment
{
    /**
     * @var array
     */
    private static $allowedExt = [
        'jpg',
        'jpeg',
        'png',
        'gif'
    ];

    /**
     * @var int 归属分类表
     */
    public static $BelongCategoryNo = 1;

    /**
     * @var int 归属产品表
     */
    public static $BelongProductNo  = 2;

    /**
     * @var int 归属规格表
     */
    public static $BelongSpecsNo    = 3;

    /**
     * @var int 归属用户表
     */
    public static $BelongUserNo     = 4;

    /**
     * @var int 归属轮播图
     */
    public static $BelongRotationNo = 5;

    /**
     * @var int 类型未缩略图
     */
    public static $TypeThumbnail = 1;

    /**
     * @var int 类型为图片列表
     */
    public static $TypeList      = 2;

    /**
     * @var int 类型为编辑器图片
     */
    public static $TypeEditor    = 3;

    private static $_errorLogDir = 'upload';



    /**
     * @var int
     */
    private static $maxFileSize = 2097152;

    /**
     * 获取图片列表
     * @param int $type
     * @param int $belongId
     * @return array
     */
    public static function GetList(int $belongType, int $belongId, int $type=2) : array
    {
        $condition = [
            'belong_type' => $belongType,
            'belong_id'   => $belongId,
            'type'        => $type
        ];
        $list = \AttachmentModel::GetListByCondition($condition);
        if( !$list )
        {
            return [];
        }
        return $list;
    }

    /**
     * 获取缩略图 一次可查多个
     * @param int $belongType   所属表类型
     * @param int $belongId     所属表ID
     * @return array
     */
    public static function GetThumbnail(int $belongType, $belongId) : array
    {
        if(is_array($belongId) && !empty($belongId)){
            $thumbnail= \AttachmentModel::GetListByCondition("type=1 and status=1 and belong_type={$belongType} and belong_id in (".implode(',',$belongId).")");
            return $thumbnail;
        }
        $thumbnail = \AttachmentModel::Detail($belongType, $belongId,0);
        if( !$thumbnail )
        {
            return [];
        }
        return $thumbnail;
    }

    /**
     * 设置封面
     * @param int $belongId    所属表ID
     * @param int $belongType  所属表类型
     * @param int $coverId
     * @return bool
     */
    public static function SetCover(int $belongId, int $belongType, int $coverId) : bool
    {
        $condition = [
            'belong_type' => $belongType,
            'belong_id'   => $belongId,
        ];
        $data = [
            'is_cover' => 0
        ];
        if( !\AttachmentModel::UpdateByCondition($condition, $data) )
        {
            return false;
        }
        $condition['id']  = $coverId;
        $data['is_cover'] = 1;
        if( !\AttachmentModel::UpdateByCondition($condition, $data) )
        {
            return false;
        }
        return true;
    }

    /**
     * 设置图片归属
     * @param array $attachmentIdList
     * @param int   $belongId
     * @return bool
     */
    public static function SetBelong(int $belongType, array $attachmentIdList, int $belongId)
    {
        return \AttachmentModel::UpdateBelongByIds($belongType, $attachmentIdList, $belongId);
    }

    /**
     * @param int $uid    上传用户ID
     * @param int $type   图片类型：1缩略图  2列表图片  3编辑器图片
     * @param int $belongType  图片所属表：1分类表  2产品表  3规格表  4用户表  5首页轮播图
     * @return array
     */
    public static function Save(int $uid=0, int $type=1, int $belongType=0) : array
    {
        $uploader = new \Uploader();
        if( !$uploader->HasFile() )
        {
            return static::_return('没有找到上传文件');
        }

        if( !$uploader->IsSizeAllowed(static::$maxFileSize) )
        {
            return static::_return('文件大小超过限制2M');
        }

        if( !$uploader->IsExtAllowed( static::$allowedExt ) )
        {
            return static::_return('不允许上传的文件类型，支持的文件格式有jpg，png，gif');
        }

        if( !$uploader->Save() )
        {
            \Log::Error(
                'file upload error : {msg}',
                [
                    '{msg}' => $uploader->GetLastErrorMsg(),
                ],
                static::$_errorLogDir
            );
            return static::_return('文件保存失败');
        }

        $result = Oss::Save(
            $uploader->GetFilePath(),
            $uploader->GetNewName()
        );
        if( !$result['result'] )
        {
            return static::_return('文件保存远程失败');
        }

        //删除本地临时文件
//        unlink( $uploader->GetFilePath() );

        $upload = \AttachmentModel::Insert([
            'belong_type' => $belongType,
            'url'         => $result['url'],
            'uid'         => $uid,
            'type'        => $type,
            'oss_bucket'  => $result['bucket'],
            'oss_object'  => $result['object']
        ]);
        if( !$upload['result'] )
        {
            return static::_return('文件数据入库失败');
        }

        return static::_return((string)$upload['insertId'],true);
    }

    /**
     * @param string $msg
     * @param bool   $result
     * @return array
     */
    private static function _return(string $msg, bool $result=false) : array
    {
        return [
            'result'  => $result,
            'message' => $msg
        ];
    }


}