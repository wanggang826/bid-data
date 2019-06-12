<?php

namespace User;

/**
 * Class User
 * @package User
 */
class User
{

    /**
     * @return array
     */
    public static function CheckSearchParam(): array
    {
        $userName = \Verifier::ReplaceSpecialSymbol(\Request::Get('account'));

        $nickName = \Verifier::ReplaceSpecialSymbol(\Request::Get('name'));

        $userSearchType = \Verifier::ReplaceSpecialSymbol((int)\Request::Get('userSearchType'));

        $condition = [];
        if ($userName != '') {
            $condition['account'] = $userName;
        }

        if ($nickName != '') {
            $condition['nick_name'] = $nickName;
        }

        if ($userSearchType > 0) {
            $condition['type'] = $userSearchType;
        }

        return $condition;
    }


    /**
     * @param string $userType
     */
    public static function ResponseList(string $userType)
    {
        $page = \Page::Info();
        $condition = static::CheckSearchParam();

        $list = \UserModel::List($userType, $condition, $page['startNum'], $page['pageSize']);
        if (!$list) {
            $list = [];
        }
        foreach ($list as $key => $value) {
            if ($value['type'] == \Yaf_Registry::get(COMMON)->managerNo) {
                if (empty($value['role'])) {
                    continue;
                }
                $roleArray = [];
                $roles = \RoleModel::GetByIds($value['role']);
                foreach ($roles as $role) {
                    $roleArray[] = $role['name'];
                }
                $list[$key]['role'] = implode(',', $roleArray);
            }
            $list[$key]['type'] = (string)$value['type'];
        }

        $count = \UserModel::Count($userType, $condition);

        \Response::Json([
            'list' => $list,
            'pageNo' => $page['pageNo'],
            'pageCount' => ceil($count / $page['pageSize']),
            'pageSize' => $page['pageSize'],
            'recordCount' => $count
        ]);
    }

    /**
     * 检查是否有模块操作权限
     * @param string $roleIds
     * @param string $funcName
     * @param int $type
     * @return bool
     */
    public static function CheckAuthorization(string $roleIds, string $funcName, int $type = 6)
    {
        if ($type == (int)\Yaf_Registry::get(COMMON)->superAdminNo) {
            goto RETURN_TRUE;
        }
        if ($type == (int)\Yaf_Registry::get(COMMON)->supplierNo) {
            goto RETURN_TRUE;
        }
        $key = strtolower(\Request::Controller() . '_' . $funcName);
        if (!\RoleModel::IsAuthorized($key, $roleIds)) {
            \Response::Error('没有访问权限', \Response::$noPermission);
        }
        RETURN_TRUE:
        return true;
    }

    /**
     * 检查token（用户是否登录）
     * @return array|bool
     */
    public static function GetUserByToken()
    {
        $token = \Request::Get('token');
        if (!\Verifier::IsToken($token)) {
            \Response::Error('令牌不正确.', \Response::$tokenError);
        }
        $user = \UserModel::GetUserByToken($token);
        if (!$user) {
            \Response::Error('令牌不正确', \Response::$tokenError);
        }

        if ($user['status'] == 0) {
            \Response::Error('用户已禁用', \Response::$BeingForbidden);
        }

        return $user;
    }

    /**
     * @brief 用户详情
     *
     * @author Hu zhangzheng
     * @created 2019/2/26 15:10
     * @param string $userType
     * @param string $func
     */
    public static function ResponseDetail(string $func)
    {
        $userLogin = static::GetUserByToken();
        $uid = (int)\Request::Param('id');

        static::CheckAuthorization($userLogin['role'], $func, $userLogin['type']);

        $user = \UserModel::Detail('', $uid);

        if (!$user) {
            \Response::Error('获取失败');
        }

        //获取角色
        if ($user['type'] == \Yaf_Registry::get(COMMON)->managerNo) {
            if (!empty($user['role'])) {
                $roleArray = [];
                $roles = \RoleModel::GetByIds($user['role']);
                foreach ($roles as $role) {
                    $roleArray[] = $role['name'];
                }
                $user['roleName'] = implode(',', $roleArray);
            }

        }

        //前端数据处理
        $user['type'] = (string)$user['type'];
        if ($user['lastLoginIp'] == '0.0.0.0') {
            $user['lastLoginIp'] = '';
        }

        if ($user['lastLoginTime'] == '0000-00-00 00:00:00') {
            $user['lastLoginTime'] = '';
        }

        //供应商权限
        if ($user['type'] == \Yaf_Registry::get(COMMON)->supplierNo) {
            $user['supplierPermission'] = json_decode($user['supplierPermission']);
        }


        //推广员二维码字符串
        if ($user['type'] == \Yaf_Registry::get(COMMON)->promoterNo) {
            $user['qrString'] = \Yaf_Registry::get(CFG)->application->get('fronturl') . '/#/?promoteStr=' . \Util_String::str2hex($user['uid']);
        }

        //获取头像
        $user['profilePicture'] = (object)[];
        $photo = Attachment::GetThumbnail(Attachment::$BelongUserNo, $user['uid']);
        if (count($photo) > 0) {
            $user['profilePicture'] = [
                'uid' => $photo['id'],
                'url' => $photo['url'],
                'status' => 'done',
                'name' => basename($photo['url']),
            ];
        }
        //获取关联驼号
        if ($user['type'] == \Yaf_Registry::get(COMMON)->camelHouseholdsNo) {
            $source = \ResourcQueueModel::getSourceByUid($uid);
            $tmp = array();
            if (!empty($source)) {
                foreach ($source as $v) {
                    $tmp[] = $v['resource_num'];
                }
            }
            $user['resourceNum'] = $tmp;
        }
        $typeName = static::_getUserTypeName($user['type']);
        \LogModel::Log($userLogin['uid'], "查看了ID为{$uid}的{$typeName}详情");

        \Response::Json(['detail' => $user]);
    }

    public static function ChangeStatus(string $func)
    {
        $user = User::GetUserByToken();
        $uid = (int)\Request::Param('id');
        $data = [
            'status' => (int)\Request::Param('status')
        ];

        if ($data['status'] != 0 && $data['status'] != 1) {
            \Response::Error('状态格式不正确');
        }

        User::CheckAuthorization($user['role'], $func, $user['type']);

        if (!\UserModel::Update($uid, '', $data)) {
            \Response::Error('状态修改失败');
        }

        $status = $data['status'] == 0 ? "禁用" : "启用";
        \LogModel::Log($user['uid'], "{$status}了ID{$uid}");

        \Response::Json(['result' => 1]);
    }

    private static function _getUserTypeName(string $userType): string
    {
        switch ($userType) {
            case \Yaf_Registry::get(COMMON)->supplierNo:
                $typeName = "供应商";
                break;
            case \Yaf_Registry::get(COMMON)->promoterNo:
                $typeName = "推广员";
                break;
            case \Yaf_Registry::get(COMMON)->wholesalerNo:
                $typeName = "批发商";
                break;
            case \Yaf_Registry::get(COMMON)->distributorNo:
                $typeName = "分销商";
                break;
            case \Yaf_Registry::get(COMMON)->managerNo:
                $typeName = "普通管理员";
                break;
            case \Yaf_Registry::get(COMMON)->camelHouseholdsNo:
                $typeName = "驼户";
                break;
            case \Yaf_Registry::get(COMMON)->sandSurfWaiterNo:
                $typeName = "滑沙服务人员";
                break;
            default:
                $typeName = "游客";
        }
        return $typeName;
    }

    /**
     * @brief 生成驼号数组
     *
     * @author Hu zhangzheng
     * @created 2019/2/25 17:51
     */
    public static function generateSourceNumArr()
    {
        $data = [];
        for ($i = 1; $i <= 625; $i++) {
            if ($i <= 300) {
                $data[] = (string)$i;
            } else if ($i > 300 && $i <= 310) {
                $data[] = (string)'B' . ($i - 300);
            } else if ($i > 310) {
                $data[] = (string)($i - 10);
            }
        }
        return $data;
    }

}