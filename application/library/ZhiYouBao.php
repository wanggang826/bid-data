<?php
/**
 * By yubin at 2018/12/10 4:17 PM.
 */
use Xml\Writer;
use Xml\Converter;

/**
 * Class ZhiYouBao
 */
class ZhiYouBao
{
    /**
     * @var
     */
    private static $_config;

    /**
     *
     */
    private static function _init()
    {
        if( !empty(static::$_config) )
        {
            return ;
        }
        static::$_config = Yaf_Registry::get(CFG_PARTNER)->zyb;
    }


    /**
     * @param string $contactName    //联系人
     * @param string $contactMobile  //联系手机
     * @param string $parentOrderId  //父订单号
     * @param float  $orderPrice     //订单金额
     * @param array  $orderDetail    //订单详情，格式如下
     [
        [
            'orderCode'  => Unique::Generate(),  //子订单号
            'price'      => 60,                  //子订单价格
            'quantity'   => 3,                   //购买数量
            'totalPrice' => 180,                 //购买总金额
            'occDate'    => date('Y-m-d'),       //游玩日期
            'goodsCode'  => 'PFT20180705204742', //商品编号，智游宝提供
            'goodsName'  => '敦煌智旅门票'         //商品名称
        ],
        [
            'orderCode'  => Unique::Generate(),
            'price'      => 60,
            'quantity'   => 3,
            'totalPrice' => 180,
            'occDate'    => date('Y-m-d'),
            'goodsCode'  => 'PFT20180705204742',
            'goodsName'  => '旅游宝门票'
        ]
     ]
     * @return array|bool
     */
    public static function PlaceOrder(string $contactName, string $contactMobile, string $parentOrderId, float $orderPrice, array $orderDetail)
    {
        static::_init();

        foreach ($orderDetail as $eachSubOrder)
        {
            if( !isset($eachSubOrder['orderCode'])  ||
                !isset($eachSubOrder['price'])      ||
                !isset($eachSubOrder['quantity'])   ||
                !isset($eachSubOrder['totalPrice']) ||
                !isset($eachSubOrder['occDate'])    ||
                !isset($eachSubOrder['goodsCode'])  ||
                !isset($eachSubOrder['goodsName'])
            )
            {
                return false;
            }
        }

        $data = [
            'PWBRequest' => [
                'transactionName' => 'SEND_CODE_REQ',
                'header'          => [
                    'application' => 'SendCode',
                    'requestTime' => date('Y-m-d'),
                ],
                'identityInfo'    => [
                    'corpCode'    => static::$_config->code,  //企业吗
                    'userName'    => static::$_config->name   //用户名
                ],
                'orderRequest'    => [
                    'order' => [
                        'certificateNo' => '',
                        'linkName'      => $contactName,    //联系人
                        'linkMobile'    => $contactMobile,  //手机
                        'orderCode'     => $parentOrderId,  //订单号
                        'orderPrice'    => $orderPrice,
                        'groupNo'       => '',
                        'payMethod'     => '',
                        'ticketOrders'  => [
                            'ticketOrder' => $orderDetail
                        ]
                    ]
                ]
            ]
        ];

        return static::_return($data);
    }

    /**
     * 获取短二维码地址
     * @param string $parentOrderId  //请求父订单号
     * @return bool|array
     * 返回如：
     * array(4) {
        ["transactionName"]=>
        string(23) "QUERY_SHORT_IMG_URL_RES"
        ["code"]=>
        string(1) "0"
        ["description"]=>
        string(6) "成功"
        ["img"]=>
        string(44) "http://t.zhiyoubao.com/t/YJ77v21544523283766"
        }
     */
    public static function GetQrShortUrl(string $parentOrderId)
    {
        static::_init();
        $data = [
            'PWBRequest' => [
                'transactionName' => 'QUERY_SHORT_IMG_URL_REQ',
                'header'          => [
                    'application' => 'SendCode',
                    'requestTime' => date('Y-m-d H:i:s')
                ],
                'identityInfo'    => [
                    'corpCode'    => static::$_config->code,  //企业吗
                    'userName'    => static::$_config->name   //用户名
                ],
                'orderRequest'    => [
                    'order'       => [
                        'orderCode' => $parentOrderId
                    ]
                ]
            ]
        ];
        return static::_return($data);
    }

    /**
     * 获取二维码地址
     * @param string $parentOrderId  //请求父订单号
     * @return bool|array
     * 返回如：
     * array(4) {
    ["transactionName"]=>
    string(17) "QUERY_IMG_URL_RES"
    ["code"]=>
    string(1) "0"
    ["description"]=>
    string(6) "成功"
    ["img"]=>
    string(101) "http://boss.zhiyoubao.com/boss/gmCheckNo.htm?aWQ9MTE0M3E04TAwMzU4JnR5cGU9T3JkZXJJbmZvJmNvZGVUeXBlPWdt"
    }
     */
    public static function GetQrUrl(string $parentOrderId)
    {
        static::_init();
        $data = [
            'PWBRequest' => [
                'transactionName' => 'QUERY_IMG_URL_REQ',
                'header'          => [
                    'application' => 'SendCode',
                    'requestTime' => date('Y-m-d H:i:s')
                ],
                'identityInfo'    => [
                    'corpCode'    => static::$_config->code,  //企业吗
                    'userName'    => static::$_config->name   //用户名
                ],
                'orderRequest'    => [
                    'order'       => [
                        'orderCode' => $parentOrderId
                    ]
                ]
            ]
        ];
        return static::_return($data);
    }

    /**
     * 取消订单
     * @param string $orderId  //请求父订单号
     * @return bool|array
     * 返回如：
     * array(4) {
    ["transactionName"]=>
    string(20) "SEND_CODE_CANCEL_RES"
    ["code"]=>
    string(1) "0"
    ["description"]=>
    string(6) "成功"
    ["retreatBatchNo"]=>
    string(32) "7c38ebe094a34b9fb92d0543e66cbdc2"
    }
     */
    public static function CancelOrder(string $orderId)
    {
        static::_init();
        $data = [
            'PWBRequest' => [
                'transactionName' => 'SEND_CODE_CANCEL_NEW_REQ',
                'header'          => [
                    'application' => 'SendCode',
                    'requestTime' => date('Y-m-d H:i:s')
                ],
                'identityInfo'    => [
                    'corpCode'    => static::$_config->code,  //企业吗
                    'userName'    => static::$_config->name   //用户名
                ],
                'orderRequest'    => [
                    'order'       => [
                        'orderCode' => $orderId
                    ]
                ]
            ]
        ];
        return static::_return($data);
    }

    /**
     * 查询检票状态
     * @param string $orderId  //请求父订单号
     * @return bool|array
     * 返回如：
     * array(4) {
    * ["transactionName"]=>
    * string(20) "SEND_CODE_CANCEL_RES"
    * ["code"]=>
    * string(1) "0"
    * ["description"]=>
    * string(6) "成功"
     * ["subOrders"]=>[
     *      'subOrder' => [
     *          'needCheckNum' => 2,
     *          'alreadyCheckNum' => 2,
     *          'returnNum'   => 2,
     *          'checkStatus' => un_check(未检), checking（开检）, checked(完成)
     *      ]
     * ]
     * }
     */
    public static function GetOrderCheckStatus(string $parentOrderId)
    {
        static::_init();
        $data = [
            'PWBRequest' => [
                'transactionName' => 'CHECK_STATUS_QUERY_REQ',
                'header'          => [
                    'application' => 'SendCode',
                    'requestTime' => date('Y-m-d H:i:s')
                ],
                'identityInfo'    => [
                    'corpCode'    => static::$_config->code,  //企业吗
                    'userName'    => static::$_config->name   //用户名
                ],
                'orderRequest'    => [
                    'order'       => [
                        'orderCode' => $parentOrderId
                    ]
                ]
            ]
        ];
        return static::_return($data);
    }

    /**
     * 统一返回格式
     * @param array  $data
     * @return array|bool
     */
    private static function _return(array $data)
    {
        //生成xml
        $orderXml = (new Writer())
            ->makeFromArray($data)
            ->getXml();

        //请求智游宝接口
        $startTime = date('Y-m-d H:i:s');
        $url = static::$_config->url;
        $requestData = [
            'xmlMsg' => $orderXml,
            'sign'   => static::_getSign($orderXml)
        ];
        $response = (new Curl($url))->Post($requestData, 'xml');
        $endTime = date('Y-m-d H:i:s');

        //判断请求状态码
        if( $response['code']=!200 )
        {
            Log::Error(
                "ZhiYouBao Error : Url : {url}, http code : {httpCode}, request : {request}, response : {response}",
                [
                    '{url}'      => $url,
                    '{httpCode}' => $response['code'],
                    '{request}'  => json_encode($requestData, JSON_UNESCAPED_UNICODE),
                    '{response}' => $response['data']
                ],
                LOG_REQUEST_DIR
            );
            return false;
        }

        //打印智游宝请求和返回debug数据
        Log::Debug(
            "ZhiYouBao : Url : {url}, http code : {httpCode}, request : {request}, response : {response} time span：{startTime} ~ {endTime}",
            [
                '{url}'      => $url,
                '{httpCode}' => $response['code'],
                '{request}'  => json_encode($requestData, JSON_UNESCAPED_UNICODE),
                '{response}' => json_encode($response['data'],JSON_UNESCAPED_UNICODE),
                '{startTime}' => $startTime,
                '{endTime}'  => $endTime
            ],
            LOG_REQUEST_DIR
        );

        //判断返回是否为xml数据，非xml数据转换array时返回false
        $data = !is_array($response['data']) ? ( new Converter($response['data']) )->ToArray() : $response['data'];
        if( !$data )
        {
            Log::Error('ZhiYouBao Error : Response data is not xml');
            return false;
        }

        return $data;
    }

    /**
     * 生成签名
     * @param string $data
     * @return string
     */
    private static function _getSign(string $data)
    {
        $privateKey = static::$_config->privateKey;
        return md5("xmlMsg={$data}".$privateKey);
    }

    /**
     * 回调签名验证
     * @param string $orderCode
     * @param string $sign
     * @param string $prefix
     * @return bool
     */
    public static function CheckCallbackSign(string $orderCode, string $sign, string $prefix='order_no') : bool
    {
        static::_init();
        $privateKey = static::$_config->privateKey;
        if( $sign==md5("{$prefix}={$orderCode}{$privateKey}") )
        {
            return true;
        }
        return false;
    }

}