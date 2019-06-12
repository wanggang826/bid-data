<?php
/**
 * Created by PhpStorm.
 * User: huangshaowen
 * Date: 2015/10/28
 * Time: 11:55
 */
use OSS\OssClient;
use OSS\Core\OssException;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
class Upload {

    private $maxSize = 0; //上传的文件大小限制 (0-不做限制)
    private $mimes = array();
    private $exts = array();
    private $maxCount = 1; //同时上传文件数量限制
    private $config;

    private $_uploadLogDir = 'upload';
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 文件保存目录
     * @var string
     */
    private $savePath;

    private $availableExt = ['gif','jpg','jpeg','bmp','png','swf'];

    /**
     * 上传错误信息
     * @var string
     */
    private $error = ''; //上传错误信息
    private $error_code = 0; //错误号

    public function __construct($rootPath, $config = array()) {
        $this->config = \Yaf_Registry::get(CFG)->aliyunOss;
        $this->rootPath = $rootPath;
        isset($config['maxSize']) && $this->maxSize = $config['maxSize'];
        isset($config['mimes']) && $this->mimes = $config['mimes'];
        isset($config['exts']) && $this->exts = $config['exts'];
        isset($config['maxCount']) && $this->maxCount = $config['maxCount'];
    }

    /**
     * 上传文件
     * @param string $files
     * @return array|bool
     */
    public function upload($files = '') {
        if ($files === '') {
            $files = $_FILES;
        }
        if(empty($files)){
            $this->error = '没有上传的文件';
            $this->_dumpLog($this->error);
            return false;
        }
        if (count($files) > $this->maxCount) {
            $this->error = '一次上传不能超过' . $this->maxCount . '个文件噢';
            $this->_dumpLog($this->error);
            return false;
        }
        if (!$this->checkRootPath()) {
            return false;
        }
        $subdir = date('Ymd');
        $this->savePath = rtrim($this->rootPath, DS) . DS . $subdir . DS;
        if (!$this->checkSavePath($this->savePath)) {
            return false;
        }
        $info = array();
        foreach ($files as $key => $file) {
            $file['name'] = strip_tags($file['name']);
            $file['ext']    =   pathinfo($file['name'], PATHINFO_EXTENSION);
            /* 文件上传检测 */
            if (!$this->check($file)){
                return false;
            }
            $file['md5']  = md5_file($file['tmp_name']);
            $file['sha1'] = sha1_file($file['tmp_name']);
            $file['mime'] = $file['type'];

            /* 对图像文件进行严格检测 */
            $ext = strtolower($file['ext']);
            if(in_array($ext, $this->availableExt)) {
                $imginfo = getimagesize($file['tmp_name']);
                if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
                    $this->error = '非法图像文件';
                    $this->_dumpLog($this->error);
                    return false;
                }
            }
            $file['saveName'] = $this->getSaveName($file);
            $file['path'] = $subdir . DS . $file['saveName'];
            if ( !$this->save($file) ) {
                return false;
            }
            $info = $file;
            $ossFile = $this->_saveToOss($info);
        }

        return empty($ossFile) ? '' : $ossFile;
    }

    public function uploadToOss($info)
    {
        $ossFile = $this->_saveToOss($info);
        return empty($ossFile) ? '' : $ossFile;
    }
    private function _dumpLog(string $error)
    {
        Log::Debug(
            'failed reason:{msg}',
            [
                '{msg}' => $error
            ],
            $this->_uploadLogDir
        );
    }

    private function _saveToOss(array $info)
    {
        $accessKeyId = $this->config->get('accessKeyId');
        $accessKeySecret = $this->config->get('accessKeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = $this->config->get('endpoint');
        // 存储空间名称
        $bucket = $this->config->get('bucket');
        // oss访问域名
        $domain = $this->config->get('domain');
        // 存储文件夹名
        $dirname = $this->config->get('dirname');

        //文件名称
        $object = $dirname.$info["saveName"];

        $filePath = $this->rootPath.$info['path'];
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        try
        {
            $ossClient->uploadFile($bucket, $object, $filePath);
        } catch(OssException $e) {
            $this->_dumpLog($e->getMessage());
            Response::Error($e->getMessage());
        }
        $this->deleteDir($this->rootPath);

        $ossFile['bucket'] = $bucket;
        $ossFile['object'] = $object;
        $ossFile['url'] = $domain.$object;

        return $ossFile;
    }


    public function deleteOssFile(string $bucket, string $object)
    {
        $accessKeyId = $this->config->get('accessKeyId');
        $accessKeySecret = $this->config->get('accessKeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = $this->config->get('endpoint');

        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $ossClient->deleteObject($bucket, $object);
        } catch(OssException $e) {
            Response::Error($e->getMessage());
        }
        return true;
    }

    /**
     * 生成保存的文件名
     * @param $file
     * @return string
     */
    private function getSaveName($file) {
        return md5(time().mt_rand(100000,999999)) . '.' . $file['ext'];
    }

    /**
     * 检测上传根目录
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    private function checkRootPath(){
        if(!(is_dir($this->rootPath) && is_writable($this->rootPath))){
            $this->error = '上传根目录不存在，请尝试手动创建';
            $this->_dumpLog($this->error);
            return false;
        }
        return true;
    }

    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    private function checkSavePath($savePath){
        /* 检测并创建目录 */
        if (!Util_File::makeDir($savePath)) {
            $this->error = "目录{$savePath}创建失败";
            $this->_dumpLog($this->error);
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($savePath)) {
                $this->error = '上传目录 ' . $savePath . ' 不可写';
                $this->_dumpLog($this->error);
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    private function save($file, $replace=true) {
        $filename = $this->savePath . $file['saveName'];
        /* 不覆盖同名文件 */
        if (!$replace && is_file($filename)) {
            $this->error = '存在同名文件' . $file['saveName'];
            $this->_dumpLog($this->error);
            return false;
        }

        /* 移动文件 */
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误';
            $this->_dumpLog($this->error);
            return false;
        }

        return true;
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    /**
     * 检查上传的文件
     * @param array $file 文件信息
     */
    private function check($file) {
        /* 文件上传失败，捕获错误代码 */
        if ($file['error']) {
            $this->error($file['error']);
            $this->_dumpLog($this->error);
            return false;
        }

        /* 无效上传 */
        if (empty($file['name'])){
            $this->error = '未知上传错误';
            $this->_dumpLog($this->error);
            return false;
        }

        /* 检查是否合法上传 */
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = '非法上传文件';
            $this->_dumpLog($this->error);
            return false;
        }

        /* 检查文件大小 */
        if (!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符';
            $this->_dumpLog($this->error);
            $this->error_code = -1001;
            return false;
        }

        /* 检查文件Mime类型 */
        //TODO:FLASH上传的文件获取到的mime类型都为application/octet-stream
        if (!$this->checkMime($file['type'])) {
            $this->error = '上传文件MIME类型不允许';
            $this->_dumpLog($this->error);
            $this->error_code = -1002;
            return false;
        }

        /* 检查文件后缀 */
        if (!$this->checkExt($file['ext'])) {
            $this->error = '上传文件后缀不允许';
            $this->_dumpLog($this->error);
            return false;
        }

        /* 通过检测 */
        return true;
    }


    /**
     * 获取错误代码信息
     * @param string $errorNo  错误号
     */
    private function error($errorNo) {
        switch ($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case 3:
                $this->error = '文件只有部分被上传';
                break;
            case 4:
                $this->error = '没有文件被上传';
                break;
            case 6:
                $this->error = '找不到临时文件夹';
                break;
            case 7:
                $this->error = '文件写入失败';
                break;
            default:
                $this->error = '未知上传错误';
        }
    }

    /**
     * 检查文件大小是否合法
     * @param integer $size 数据
     */
    private function checkSize($size) {
        return !($size > $this->maxSize) || (0 == $this->maxSize);
    }

    /**
     * 检查上传的文件MIME类型是否合法
     * @param string $mime 数据
     */
    private function checkMime($mime) {
        return empty($this->mimes) ? true : in_array(strtolower($mime), $this->mimes);
    }

    /**
     * 检查上传的文件后缀是否合法
     * @param string $ext 后缀
     */
    private function checkExt($ext) {
        return empty($this->exts) ? true : in_array(strtolower($ext), $this->exts);
    }

    private function deleteDir($path){
        if ( !is_dir($path) )
        {
            return false;
        }
        $p = scandir($path);
        foreach($p as $val){
            if ($val !== "." && $val !== "..")
            {       //排除当前目录与父级目录
                if(is_dir($path.$val))
                {
                    $this->deleteDir($path.$val.'/');
                    @rmdir($path.$val.'/');
                }
                else
                {
                    unlink($path.$val);
                }
            }
        }
    }
}