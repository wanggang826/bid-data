<?php

class WxAuth
{
    private $appid;
    private $appsecret;
    private $callback;

    public function __construct()
    {
        $WxConf          = Yaf_Registry::get(CFG)->auth->get('wx');
        $this->appid     = $WxConf->appid;
        $this->appsecret = $WxConf->appsecret;
        $this->callback  = $WxConf->callback;

    }

    public function getUser()
    {
        if (is_null(Request::Get('code')) || Request::Get('code') == "") {
            $callback = $this->callback;
            $this->getCode($callback);
        } else {
            $code     = Request::Get('code');
            $data     = $this->getAccessToken($code);
            $data_all = $this->getUserInfo($data['access_token'], $data['openid']);
            return $data_all;
        }
    }

    private function getCode($callback)
    {
        $appid = $this->appid;
        $scope = 'snsapi_userinfo';
        $state = md5(uniqid(rand(), true));
        $url   = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($callback) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
        header("Location:$url");
    }

    private function getAccessToken($code)
    {
        $appid     = $this->appid;
        $appsecret = $this->appsecret;
        $url       = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $user      = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            echo 'error:' . $user->errcode . '<hr>msg  :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user), true);
        return $data;
    }

    private function getUserInfo($access_token, $openid)
    {
        $url  = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $user = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            echo 'error:' . $user->errcode . '<hr>msg  :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user), true);
        return $data;
    }
}
