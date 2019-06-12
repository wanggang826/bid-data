<?php

namespace AliPay;

use AliPay\Aop\Request\AlipayTradeWapPayRequest;
use AliPay\Aop\Request\AlipayTradeRefundRequest;
use AliPay\Aop\Request\AlipayTradeFastpayRefundQueryRequest;
use AliPay\Aop\AopClient;

/**
 * Class Alipay 支付宝支付类
 * @package AliPay
 */
class Alipay
{

    /**
     * @var string
     */
    private static $apiVersion   = '1.0';
    /**
     * @var string
     */
    private static $returnFormat = 'json';
    /**
     * @var string
     */
    private static $charset      = 'utf-8';
    /**
     * @var bool
     */
    private static $debug        = true;

    /**
     * @return mixed
     */
    public static function _getConfig()
    {
        return \Yaf_Registry::get(CFG)->alipay;
    }

    /**
     * @return AopClient
     */
    private static function _getAopClient()
    {
        $cfg = self::_getConfig();
        $aop = new AopClient ();
        $aop->apiVersion  = self::$apiVersion;
        $aop->postCharset = self::$charset;
        $aop->format      = self::$returnFormat;
        $aop->debugInfo   = self::$debug;

        $aop->appId         = $cfg->get('appId');
        $aop->signType      = $cfg->get('signType');
        $aop->gatewayUrl    = $cfg->get('gatewayUrl');
        $aop->rsaPrivateKey = $cfg->get('merchantPrivateKey');
        $aop->alipayrsaPublicKey = $cfg->get('alipayPublicKey');

        return $aop;
    }

    /**
     * 返回前端调用url
     * @param string $subject     主题
     * @param string $body        内容
     * @param string $outTradeNo  订单号
     * @param float $totalAmount  金额
     * @param string $timeout     请求超时时间
     * @return bool|string
     */
    public static function Url(string $subject, string $body, string $outTradeNo, float $totalAmount, string $timeout='1m')
    {
        $config  = self::_getConfig();
        $request = new AlipayTradeWapPayRequest();
        $request -> setNotifyUrl( $config->get('notifyUrl') )
                 -> setReturnUrl( $config->get('returnUrl'))
                 -> setApiMethodName('alipay.trade.app.pay')
                 -> setBizContent(json_encode(
                     [
                     'subject'      => $subject,
                     'body'         => $body,
                     'out_trade_no' => $outTradeNo,
                     'total_amount' => $totalAmount,
                     'timeout_express' => $timeout,
                     'product_code'  => 'QUICK_MSECURITY_PAY'
                     ],
                     JSON_UNESCAPED_UNICODE
                 ));
        try
        {
            $result = self::_getAopClient()->buildUrl($request,null,null,true)[0];
        }
        catch ( \Exception $e )
        {
            \Log::Error(
                "Alipay::Url : self::_getAopClient()->buildUrl error : {error}",
                [
                    '{error}' => $e->getMessage()
                ],
                LOG_ALI
            );
            return false;
        }
        return $result;
    }

    /**
     * 唤起支付
     * @param string $subject     主题
     * @param string $body        内容
     * @param string $outTradeNo  订单号
     * @param float $totalAmount  金额
     * @param string $timeout     请求超时时间
     * @return bool|string
     */
    public static function PayPage(string $subject, string $body, string $outTradeNo, float $totalAmount, string $timeout='1m')
    {
        $config  = self::_getConfig();
        $request = new AlipayTradeWapPayRequest();
        $request -> setNotifyUrl( $config->get('notifyUrl') )
            -> setReturnUrl( $config->get('returnUrl'))
            -> setBizContent(json_encode(
                [
                    'subject'      => $subject,
                    'body'         => $body,
                    'out_trade_no' => $outTradeNo,
                    'total_amount' => $totalAmount,
                    'timeout_express' => $timeout
                ],
                JSON_UNESCAPED_UNICODE
            ));
        try
        {
            $result = self::_getAopClient()->pageExecute($request,'POST');
        }
        catch ( \Exception $e )
        {
            \Log::Error(
                "Alipay::PayPage : self::_getAopClient()->pageExecute error : {error}",
                [
                    '{error}'=>$e->getMessage()
                ],
                LOG_ALI
            );
            return false;
        }
        return $result;
    }

    /**
     * 退款接口
     * @param string $tradeNo      支付宝交易号
     * @param string $outTradeNo   订单号
     * @param float $refundFee     退款金额
     * @param string $refundReason 退款原因
     * @param string $refundNo     退款编号
     * @return bool|\SimpleXMLElement
     */
    public static function Refund(string $tradeNo, string $outTradeNo, float $refundFee, string $refundReason, string $refundNo)
    {
        $request = new AlipayTradeRefundRequest();
        $request -> setBizContent(json_encode(
                    [
                        'out_trade_no'  => $outTradeNo,
                        'trade_no'      => $tradeNo,
                        'refund_amount' => $refundFee,
                        'refund_reason' => $refundReason,
                        'out_request_no' => $refundNo,

                    ],
                    JSON_UNESCAPED_UNICODE
            ));
        try
        {
            $startTime = date('Y-m-d H:i:s');
            $result = self::_getAopClient()->Execute($request,"post")->alipay_trade_refund_response;
            $endTime   = date('Y-m-d H:i:s');

        }
        catch (\Exception $e)
        {
            \Log::Error(
                "Alipay::Refund : self::_getAopClient()->Execute error : {error}",
                [
                    '{error}'=>$e->getMessage()
                ],
                LOG_ALI
            );
            return false;
        }
        \Log::Debug(
            'Alipay::Refund : $tradeNo : {tradeNo}, $outTradeNo : {outTradeNo}, $totalFee : {totalFee}, $refundFee : {refundFee}, $refundNo : {refundNo}, result : {result}, time span : {timeSpan}',
            [
                '{outTradeNo}' => $outTradeNo,
                '{tradeNo}'    => $tradeNo,
                '{refundFee}'  => $refundFee,
                '{refundNo}'   => $refundNo,
                '{refundReason}' => $refundReason,
                '{result}'     => json_encode($result, JSON_UNESCAPED_UNICODE),
                '{timeSpan}'   => "{$startTime} ~ {$endTime}"
            ],
            LOG_ALI
        );

        if( $result->code!='10000' )
        {
            \Log::Error(
                'Alipay::Refund : $tradeNo : {tradeNo}, $outTradeNo : {outTradeNo}, $totalFee : {totalFee}, $refundFee : {refundFee}, $refundNo : {refundNo}, result : {result}',
                [
                    '{outTradeNo}' => $outTradeNo,
                    '{tradeNo}'    => $tradeNo,
                    '{refundFee}'  => $refundFee,
                    '{refundNo}'   => $refundNo,
                    '{refundReason}' => $refundReason,
                    '{result}'     => json_encode($result, JSON_UNESCAPED_UNICODE)
                ],
                LOG_ALI
            );
            return false;
        }

        return $result;
    }

    /**
     * 退款查询接口
     * @param string $tradeNo       支付宝交易号
     * @param string $outTradeNo    订单号
     * @param string $outRequestNo  退款编号
     * @return bool|mixed|\SimpleXMLElement
     */
    public static function QueryRefund(string $tradeNo, string $outTradeNo, string $outRequestNo)
    {
        $request = new AlipayTradeFastpayRefundQueryRequest();
        $request->setBizContent(json_encode([
                'trade_no'      => $tradeNo,
                'out_trade_no'  => $outTradeNo,
                'out_request_no' => $outRequestNo
            ],
            JSON_UNESCAPED_UNICODE)
        );
        try
        {
            $result = self::_getAopClient()->Execute($request,"post")->alipay_trade_fastpay_refund_query_response;
        }
        catch (\Exception $e)
        {
            \Log::Error(
                "Alipay::QueryRefund : QueryRefund error : {error}",
                [
                    '{error}' => $e->getMessage()
                ],
                LOG_ALI
            );
            return false;
        }
        return $result;
    }


    /**
     * 签名验证
     * @param array $data
     * @return bool
     */
    public static function NotifySignCheck(array $data) : bool
    {
        $config = self::_getConfig();
        $aop = new AopClient();
        $aop -> alipayrsaPublicKey = $config->get('alipayPublicKey');
        return $aop->rsaCheckV1($data, $config->get('alipayPublicKey'), $config->get('signType'));
    }

}