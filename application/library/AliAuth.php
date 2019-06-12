<?php
use AliPay\Aop\AlipaySystemOauthTokenRequest;
use AliPay\Aop\AlipayUserInfoShareRequest;
use AliPay\Aop\AopClient;

class AliAuth
{
    private $appid;
    private $rsaPrivateKey;
    private $alipayrsaPublicKey;
    private $redirectUri;

    public function __construct()
    {
        $AliConf                  = Yaf_Registry::get(CFG)->auth->get('ali');
        $this->appid              = $AliConf->appid;
        $this->rsaPrivateKey      = $AliConf->rsaPrivateKey;
        $this->alipayrsaPublicKey = $AliConf->alipayrsaPublicKey;
        $this->redirectUri        = $AliConf->redirectUri;
    }

    public function getUser()
    {
        $code = Request::Get('auth_code');
        if (is_null($code) || $code == "") {
            $appid        = $this->appid;
            $redirect_uri = urlencode($this->redirectUri);
            $url          = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={$appid}&scope=auth_user&redirect_uri={$redirect_uri}";
            header("Location:" . $url);
        }
        $aop                     = new AopClient();
        $aop->gatewayUrl         = 'https://openapi.alipay.com/gateway.do';
        $aop->appId              = $this->appid;
        $aop->rsaPrivateKey      = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->apiVersion         = '1.0';
        $aop->signType           = 'RSA2';
        $aop->postCharset        = 'UTF-8';
        $aop->format             = 'json';

        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($code);
        $result       = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $accessToken  = $result->$responseNode->access_token;
        $request      = new AlipayUserInfoShareRequest();
        $result       = $aop->execute($request, $accessToken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode   = $result->$responseNode->code;

        return $result->$responseNode;
    }
}
