<?php


namespace WeChatPay;

use WeChatPay\Lib\WxPayApi;
use WeChatPay\Lib\WxPayException;
use WeChatPay\Lib\WxPayRefund;
use WeChatPay\Lib\WxPayUnifiedOrder;
use WeChatPay\Lib\WxPayRefundQuery;

require_once 'Lib/WxPayNotify.php';
require_once 'Lib/WxPayData.php';

/**
 * Class WeChatPay
 * @package WeChatPay
 */
class WeChatPay
{
    /**
     * @var string
     */
    private static $payTradeType = "JSAPI";

    /**
     * @return mixed
     */
    private static function _getConfig()
    {
        return \Yaf_Registry::get(CFG)->weChatPay;
    }

    /**
     * 支付
     * @param string $body     支付主题
     * @param string $attach   支付说明
     * @param string $outTradeNo   平台订单号
     * @param float $fee       订单金额
     * @param string $tag      标签
     * @param string $subAppId       子商户公众号id
     * @param string $subMerchantId  子商户商户id
     * @return string|false
     */
    public static function Pay(string $body, string $attach, string $outTradeNo, float $fee, string $tag, string $subAppId='', string $subMerchantId='')
    {
        try
        {
            $tools = new JsApiPay();
            $input = (new WxPayUnifiedOrder())
                    ->SetSubAppId( $subAppId)
                    ->SetSubMerchantId( $subMerchantId)
                    ->SetAttach( $attach )
                    ->SetBody( $body)
                    ->SetAttach( $attach )
                    ->SetOut_trade_no( $outTradeNo )
                    ->SetTotal_fee( $fee )
                    ->SetTime_start( date("YmdHis") )
                    ->SetTime_expire( date("YmdHis", time() + 600) )
                    ->SetGoods_tag( $tag )
                    ->SetNotify_url( self::_getConfig()->get('notifyUrl') )
                    ->SetTrade_type( self::$payTradeType )
                    ->SetOpenid( $tools->GetOpenid() );
            try
            {
                $order = WxPayApi::unifiedOrder( (new WxPayConfig()), $input);
            }
            catch (WxPayException $e)
            {
                \Log::Error(
                    'WxPayApi::unifiedOrder Error outTradeNo : {outTradeNo}, fee:{fee}, body:{body}, attach:{attach}, tag:{tag} message: {message},',
                    [
                        '{outTradeNo}' => $outTradeNo,
                        '{fee}'        => $fee,
                        '{body}'       => $body,
                        '{attach}'     => $attach,
                        '{tag}'        => $tag,
                        '{message}'    => $e->errorMessage()
                    ],
                    LOG_WX
                );
                return false;
            }
            return $tools->GetJsApiParameters($order);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WeChatPay :JsApiPay->GetJsApiParameters Error: outTradeNo : {outTradeNo}, fee:{fee}, body:{body}, attach:{attach}, tag:{tag} message: {message},',
                [
                    '{outTradeNo}' => $outTradeNo,
                    '{fee}'        => $fee,
                    '{body}'       => $body,
                    '{attach}'     => $attach,
                    '{tag}'        => $tag,
                    '{message}'    => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }

    public static function getEditorAddress()
    {
        return (new JsApiPay())->GetEditAddressParameters();
    }

    /**
     * 使用微信交易号退款
     * @param string $transactionId   微信支付交易号
     * @param float $totalFee        订单总金额
     * @param float $refundFee       退款金额
     * @param string $refundNo        退款编号
     * @param string $subAppId        子商户微信公众号id
     * @param string $subMerchantId   子商户id
     * @return array|bool
     */
    public static function RefundByTransactionId(string $transactionId, float  $totalFee, float $refundFee, string $refundNo, string $subAppId='', string $subMerchantId='')
    {
        try
        {
            $input = (new WxPayRefund())
                    ->SetSubAppId( $subAppId)
                    ->SetSubMerchantId( $subMerchantId)
                    ->SetTransaction_id($transactionId)
                    ->SetTotal_fee($totalFee*100)
                    ->SetRefund_fee($refundFee*100)
                    ->SetOut_refund_no($refundNo);

            $config = new WxPayConfig();
            $input->SetOp_user_id($config->GetMerchantId());
            return  WxPayApi::refund($config, $input);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WxPayApi::refund error : {message}, transactionId: {transactionId}, totalFee:{totalFee}, refundFee:{refundFee}, refundNo: {$refundNo}',
                [
                    '{refundNo}'      => $refundNo,
                    '{transactionId}' => $transactionId,
                    '{totalFee}'      => $totalFee,
                    '{refundFee}'     => $refundFee,
                    '{message}'       => $e->errorMessage()
                ],
                LOG_WX
                );
        }
        return false;
    }

    /**
     * 使用平台订单号退款
     * @param string $outTradeNo   平台订单号
     * @param float $totalFee     订单总金额
     * @param float $refundFee    退款金额
     * @param string $refundNo     退款编号
     * @param string $subAppId        子商户微信公众号id
     * @param string $subMerchantId   子商户id
     * @return array|bool
     */
    public static function RefundByOutTradeNo(string $outTradeNo, float  $totalFee, float $refundFee, string $refundNo, string $subAppId='', string $subMerchantId='')
    {
        try
        {
            $input = (new WxPayRefund())
            ->SetSubAppId( $subAppId)
            ->SetSubMerchantId( $subMerchantId)
            ->SetOut_trade_no($outTradeNo)
            ->SetTotal_fee($totalFee*100)
            ->SetRefund_fee($refundFee*100)
            ->SetOut_refund_no($refundNo);

            $startTime = date('Y-m-d H:i:s');

            $config = new WxPayConfig();
            $input->SetOp_user_id($config->GetMerchantId());
            $result = WxPayApi::refund($config, $input);

            $endTime = date('Y-m-d H:i:s');

            \Log::Debug(
                'WeChatPay::RefundByOutTradeNo : $outTradeNo : {outTradeNo}, $totalFee : {totalFee}, $refundFee : {refundFee}, $refundNo : {refundNo}, result : {result}, time span : {timeSpan}',
                [
                    '{outTradeNo}' => $outTradeNo,
                    '{totalFee}'   => $totalFee,
                    '{refundFee}'  => $refundFee,
                    '{refundNo}'   => $refundNo,
                    '{result}'     => json_encode($result, JSON_UNESCAPED_UNICODE),
                    '{timeSpan}'   => "{$startTime} ~ {$endTime}"
                ],
                LOG_WX
            );
            if( $result['return_code']=='FAIL' )
            {
                \Log::Error(
                    'WeChatPay::RefundByOutTradeNo : $outTradeNo : {outTradeNo}, $totalFee : {totalFee}, $refundFee : {refundFee}, $refundNo : {refundNo}, result : {result}',
                    [
                        '{outTradeNo}' => $outTradeNo,
                        '{totalFee}'   => $totalFee,
                        '{refundFee}'  => $refundFee,
                        '{refundNo}'   => $refundNo,
                        '{result}'     => json_encode($result, JSON_UNESCAPED_UNICODE)
                    ],
                    LOG_WX
                );
                return false;
            }
            return $result;
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'RefundByOutTradeNo : WxPayApi::refund error : {message}, outTradeNo: {outTradeNo}, totalFee:{totalFee}, refundFee:{refundFee}, refundNo: {refundNo}',
                [
                    '{refundNo}'   => $refundNo,
                    '{outTradeNo}' => $outTradeNo,
                    '{totalFee}'   => $totalFee,
                    '{refundFee}'  => $refundFee,
                    '{message}'    => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }

    /**
     * 按照微信交易号查询
     * @param string $transactionId
     * @return array|bool
     */
    public static function RefundQueryByTransactionId(string $transactionId)
    {
        try
        {
            $input = new WxPayRefundQuery();
            $input->SetTransaction_id($transactionId);
            $config = new WxPayConfig();
            return WxPayApi::refundQuery($config, $input);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WeChatPay::RefundQueryByTransactionId : WxPayApi::refundQuery error : transactionId: {transactionId} :message:{message}',
                [
                    '{transactionId}' => $transactionId,
                    '{message}'       => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }

    /**
     * 按照订单号查询
     * @param string $outTradeNo
     * @return array|bool
     */
    public static function RefundQueryByOutTradeNo(string $outTradeNo)
    {
        try
        {
            $input = new WxPayRefundQuery();
            $input->SetOut_trade_no($outTradeNo);
            $config = new WxPayConfig();
            return WxPayApi::refundQuery($config, $input);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WeChatPay::RefundQueryByOutTradeNo : WxPayApi::refundQuery error : outTradeNo: {outTradeNo} :message:{message}',
                [
                    '{outTradeNo}' => $outTradeNo,
                    '{message}'    => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }

    /**
     * 按照平台退款编号查询
     * @param string $outRefundNo
     * @return array|bool
     */
    public static function RefundQueryByOutRefundNo(string $outRefundNo)
    {
        try
        {
            $input = new WxPayRefundQuery();
            $input->SetOut_refund_no($outRefundNo);
            $config = new WxPayConfig();
            return WxPayApi::refundQuery($config, $input);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WeChatPay::RefundQueryByOutTradeNo : WxPayApi::refundQuery error : outRefundNo: {outRefundNo} :message:{message}',
                [
                    '{outRefundNo}'   => $outRefundNo,
                    '{message}'    => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }

    /**
     * 按照微信退款id查询
     * @param string $refundId
     * @return array|bool
     */
    public static function RefundQueryByOutRefundId(string $refundId)
    {
        try
        {
            $input = new WxPayRefundQuery();
            $input->SetRefund_id($refundId);
            $config = new WxPayConfig();
            return WxPayApi::refundQuery($config, $input);
        }
        catch(WxPayException $e)
        {
            \Log::Error(
                'WeChatPay::RefundQueryByOutTradeNo : WxPayApi::refundQuery error : refundId: {refundId} :message:{message}',
                [
                    '{refundId}'   => $refundId,
                    '{message}'    => $e->errorMessage()
                ],
                LOG_WX
            );
        }
        return false;
    }


}