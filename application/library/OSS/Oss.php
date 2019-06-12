<?php
/**
 * By yubin at 2018/11/21 11:08 AM.
 */

namespace OSS;

use OSS\Core\OssException;


/**
 * Class Oss
 * @package OSS
 */
class Oss
{
    /**
     * @var bool
     */
    private static $_isInitialize = false;

    /**
     * @var
     */
    private static $_accessKey;

    /**
     * @var
     */
    private static $_keySecret;

    /**
     * @var
     */
    private static $_endPoint;

    /**
     * @var
     */
    private static $_bucket;

    /**
     * @var
     */
    private static $_domain;

    /**
     * @var
     */
    private static $_dirName;
    /**
     * @var
     */
    private static $_ossClient;

    /**
     * initialize configuration of oss
     */
    private static function _init()
    {
        if (static::$_isInitialize) {
            return;
        }
        $config = \Yaf_Registry::get(CFG)->aliyunOss;
        static::$_accessKey = $config->get('accessKeyId');
        static::$_keySecret = $config->get('accessKeySecret');
        static::$_endPoint = $config->get('endpoint');
        static::$_bucket = $config->get('bucket');
        static::$_domain = $config->get('domain');
        static::$_dirName = $config->get('dirname');

        static::$_isInitialize = true;
    }

    private static function createOssClient()
    {
        if (!static::$_ossClient) {
            static::_init();
            static::$_ossClient = new OssClient(
                static::$_accessKey,
                static::$_keySecret,
                static::$_endPoint
            );
        }
        return static::$_ossClient;
    }


    /**
     * save local file to oss
     * @param string $filePath
     * @param string $saveName
     * @return array
     */
    public static function Save(string $filePath, string $saveName, bool $isUeditor = false): array
    {
        $result = true;
        if ($isUeditor) {
            static::$_dirName = '';
        }
        $object = static::$_dirName . $saveName;
        if (strpos($object, '/') == 0) {
            $object = substr($object, 1);
        }
        try {
            $client = static::createOssClient();

            $client->uploadFile(
                static::$_bucket,
                $object,
                $filePath
            );
        } catch (OssException $e) {
            \Log::Error(
                'oss upload error: {msg}',
                [
                    '{msg}' => $e->getMessage()
                ],
                'oss'
            );
            $result = false;
        }

        return [
            'result' => $result,
            'bucket' => static::$_bucket,
            'object' => $object,
            'url' => static::$_domain . $object
        ];
    }

    /**
     * @param array $files
     * @return array
     */
    public static function MultiSave(array $files): array
    {
        $return = [];
        foreach ($files as $file) {
            if (!isset($file['filePath']) || !isset($file['saveName'])) {
                continue;
            }
            $return[] = static::Save($file['filePath'], $file['saveName']);
        }
    }

    /**
     * @param string $object
     * @param string $bucket
     * @return bool
     */
    public static function Delete(string $object, string $bucket = ''): bool
    {
        $bucket = empty($bucket) ? static::$_bucket : $bucket;
        try {
            $client = static::createOssClient();
            $client->deleteObject($bucket, $object);
        } catch (OssException $e) {
            \Response::Error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @brief 上传base64格式图片
     *
     * @author Hu zhangzheng
     * @created 2019/4/2 16:51
     * @param string $filePath
     * @param string $saveName
     * @param bool $isUeditor
     * @return array
     */
    public static function SaveBase64(string $img, string $saveName, bool $isUeditor = false): array
    {
        $result = true;
        if ($isUeditor) {
            static::$_dirName = '';
        }
        $object = static::$_dirName . $saveName;
        if (strpos($object, '/') == 0) {
            $object = substr($object, 1);
        }
        try {
            $client = static::createOssClient();
            $client->putObject(static::$_bucket, $object, $img);

        } catch (OssException $e) {
            \Log::Error(
                'oss upload error: {msg}',
                [
                    '{msg}' => $e->getMessage()
                ],
                'oss'
            );
            $result = false;
        }

        return [
            'result' => $result,
            'bucket' => static::$_bucket,
            'object' => $object,
            'url' => static::$_domain . $object
        ];
    }

    /**
     * @brief 列出oss指定列表下的文件,oss没有目录概念，从前往后匹配
     *
     * @author Hu zhangzheng
     * @created 2019/4/2 18:55
     * @throws OssException
     */
    public static function ListObject($prefix, $delimiter = '/', $nextMarker = '', $maxkeys = 50)
    {
        global $storageList;
        if (strpos($prefix, '/') == 0) {
            $prefix = substr($prefix, 1);
        }
        $client = static::createOssClient();

        $fileList = []; // 获取的文件列表, 数组的一阶表示分页结果
        $dirList = []; // 获取的目录列表, 数组的一阶表示分页结果

        while (true) {
            $options = array(
                'delimiter' => $delimiter,
                'prefix' => $prefix,
                'max-keys' => $maxkeys,
                'marker' => $nextMarker,
            );
            try {
                $listObjectInfo = $client->listObjects(static::$_bucket, $options);
                // 得到nextMarker, 从上一次 listObjects 读到的最后一个文件的下一个文件开始继续获取文件列表, 类似分页
            } catch (OssException $e) {
                return; // 发送错误信息
            }
            $nextMarker = $listObjectInfo->getNextMarker();
            $objectList = $listObjectInfo->getObjectList(); // object list
            $prefixList = $listObjectInfo->getPrefixList(); // directory list
            $fileList[] = $objectList;
            $dirList[] = $prefixList;
            if ($nextMarker === '') break;
        }
        foreach ($fileList as $item) {
            foreach ($item as $row) {
                $storageList['file'][] = static::objectInfoParse($row);
            }
        }

        foreach ($dirList as $item) {
            foreach ($item as $row) {
                $storageList['dir'][] = static::prefixInfoParse($row);
            }
        }
        if (!empty($storageList['dir'])) {
            foreach ($storageList['dir'] as $k => $dir) {
                unset($storageList['dir'][$k]);
                static::ListObject($dir['dir'], '/', '', 50);
            }
        }
        return $storageList['file'];
    }

    /* 解析 prefixInfo 类 */
    private static function prefixInfoParse($prefixInfo)
    {
        return [
            'dir' => $prefixInfo->getPrefix(),
        ];
    }

    /* 解析 objectInfo 类 */
    private static function objectInfoParse($objectInfo)
    {
        return [
            'url' => static::$_domain . $objectInfo->getKey(),
//            'size' => $objectInfo->getSize(),
            'mtime' => $objectInfo->getLastModified(),
        ];
    }
}