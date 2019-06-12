<?php

use Xml\Converter;

/**
 * Class Curl
 */
class Curl
{
    /**
     * @var resource
     */
    private $_curl;

    /**
     * @var string
     */
    private $_url;

    /**
     * @var curl response
     */
    private $_response;

    /**
     * @var bool
     */
    private $_isShowReturnHeader = false;

    /**
     * @var
     */
    private $_responseHeaderSize;
    private $_curlInfo;

    /**
     * Curl constructor.
     * @param string $url
     * @param int $expireSeconds
     * @param bool $notDirectShow
     */
    public function __construct(string $url, int $expireSeconds=30, bool $notDirectShow=true)
    {
        $this->_url  = $url;
        $this->_curl = curl_init();
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt( $this->_curl, CURLOPT_RETURNTRANSFER, $notDirectShow);
        //设置请求url
        curl_setopt( $this->_curl, CURLOPT_URL, $url);
        //超时时间设置
        curl_setopt( $this->_curl, CURLOPT_TIMEOUT, $expireSeconds);
    }

    /**
     * set header for request
     * @param array $header
     * @param bool $isShowReturnHeader
     * @param bool $isAllowedTraceRequestHeader
     * @return $this
     */
    public function Header(array $header, bool $isShowReturnHeader=true, bool $isAllowedTraceRequestHeader=true)
    {
        $this->_isShowReturnHeader = $isShowReturnHeader;
        //返回response头部信息
        curl_setopt( $this->_curl, CURLOPT_HEADER, $isShowReturnHeader);
        //设置请求头
        curl_setopt( $this->_curl, CURLOPT_HTTPHEADER, $header);
        //是否允许追踪求情头
        curl_setopt( $this->_curl, CURLINFO_HEADER_OUT, $isAllowedTraceRequestHeader);

        return $this;
    }

    /**
     * echo request header
     * @return $this
     */
    public function RequestHeader()
    {
        //TRUE 时追踪句柄的请求字符串，从 PHP 5.1.3 开始可用。这个很关键，就是允许你查看请求header
        $header = curl_getinfo( $this->_curl,CURLINFO_HEADER_OUT );
        echo($header);
        return $this;
    }
    
    public function setShowReturnHeader(){
        $this->_isShowReturnHeader = TRUE;
    }

    /**
     * @return bool|string
     */
    public function ResponseHeader()
    {
        if( $this->_isShowReturnHeader )
        {
            return substr($this->_response, 0, $this->_responseHeaderSize);
        }
        return false;
    }

    /**
     * @param bool $verifyCertificate
     * @param bool $verifyHost
     * @return $this
     */
    public function Ssl(bool $verifyCertificate=false, bool $verifyHost=false)
    {
        // https请求 不验证证书
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYPEER, $verifyCertificate);
        // https请求 不验证主机
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYHOST, $verifyHost);
        return $this;
    }

    /**
     * send post request
     * @param array $data
     * @return array
     */
    public function Post(array $data=[],$type='JSON')
    {
        //是否为post提交
        curl_setopt( $this->_curl, CURLOPT_POST, 1);
        //post 数据
        curl_setopt( $this->_curl, CURLOPT_POSTFIELDS, $data);
        //执行请求
        return $this->_response($data,$type);
    }

    /**
     * send get request
     * @param array $data
     * @return array
     */
    public  function Get(array $data=[],$type='JSON') : array
    {
        //设置抓取的url
        curl_setopt( $this->_curl, CURLOPT_URL, $this->_rebuildUrl($this->_url,$data));
        return $this->_response($data,$type);
    }

    /**
     * return http code of the request
     * @return int
     */
    private function _statusCode() : int
    {
        //返回状态吗
        return curl_getinfo( $this->_curl,CURLINFO_HTTP_CODE);
    }

    /**
     * rebuild url for get request
     * @param string $url
     * @param array $data
     * @return string
     */
    private function _rebuildUrl(string $url, array $data)
    {
        $count = 0;
        foreach ($data as $key => $val)
        {
            $count++ ;
            if ( $count==1 )
            {
                $url = $url.'?'.$key.'='.(string)$val;
                continue ;
            }
            $url = $url.'&'.$key.'='.(string)$val;
        }
        return $url;
    }

    /**
     * return specified result
     * @return array
     */
    private function _response($data,$type)
    {
        //执行请求
        $this->_response = curl_exec( $this->_curl );
        $this->_curlInfo = curl_getinfo( $this->_curl);
        $this->_responseHeaderSize = $this->_curlInfo['header_size'];
        
        $response = $this->_response;
        if( $this->_isShowReturnHeader )
        {
            $response = substr($this->_response, $this->_responseHeaderSize);
        }

        Log::Debug(
            'URL:[{url}] | REQUEST:[{request}] | RESULT:[{result}] | HTTP_INFO:[{http_info}] | code : {code}',
            [
                '{url}'    => $this->_url,
                '{request}'  => json_encode($data),
                '{result}' => $response,
                '{http_info}'   => json_encode($this->_curlInfo),
                '{code}'    => $this->_statusCode()
            ],
            LOG_REQUEST_DIR
        );
        
        if (empty($response)) {
            return null;
        }
        
        //兼容xml数据返回json情况
        json_decode($response);
        $type = (json_last_error() == JSON_ERROR_NONE) ? 'json' : $type;
        
        switch ($type) {
            case 'json':
            case 'JSON':
                if (PHP_INT_MAX == 2147483647) {
                    $res = json_decode(preg_replace('#(?<=[,\{\[])\s*("\w+"):(\d{6,})(?=\s*[,\]\}])#si', '${1}:"${2}"', $response), true);
                } else {
                    $res = json_decode($response, true);
                }
                break;
            case 'xml':
                $res = ( new Converter($response) )->ToArray();
                break;
            default:
                $res = $response;
        }
        
        return [
            'code' => $this->_statusCode(),
            'data' => $res
        ];
    }

    /**
     * destroy curl resource
     */
    public function __destruct()
    {
        //关闭连接
        curl_close( $this->_curl );
    }

}