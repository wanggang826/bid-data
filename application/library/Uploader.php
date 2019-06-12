<?php


/**
 * Class Uploader
 */
class Uploader
{
    /**
     * default configuration
     */
    private $config =[
        'savePath' => APPLICATION_PATH.'/upload/',
        'maxSize'  => 2097152,    // 2M
        'allowedExt' => [
            'jpg',
            'png',
            'jpeg',
            'rar',
            'zip',
            'doc',
            'docx',
            'ppt',
            'pptx',
            'xls',
            'xlsx'
        ]
    ];

    /**
     * file : file information
     * @var
     */
    private $file;

    /**
     * newFileName : file name for saving
     * @var null
     */
    private $_newFileName = '';

    /**
     * @var string
     */
    private $_fileStorePath = '';

    /**
     * extension : upload file extension
     * @var null
     */
    private $extension = null;

    private $_lastErrorMsg;

    /**
     * Upload constructor.
     * @param string $name
     */
    public function __construct(string $name='')
    {
        if( (!empty($name) && !isset($_FILES[$name])) || count($_FILES)==0 )
        {
            Log::Error('upload file not found');
            return ;
        }

        if( empty($name) )
        {
            foreach( $_FILES as $key=>$val )
            {
                $name = $key;
                break ;
            }
        }

        $this->file = $_FILES[$name];
        $this->_initConfig();
        $this->_getExt();
    }

    /**
     * checkConfig
     */
    private function _initConfig()
    {
        //todo
    }

    /**
     * IsExtAllowed : check if the upload file extension is allowed
     * @return bool
     */
    public function IsExtAllowed(array $allowExt=[]) :bool
    {
        if( is_null($this->extension) )
        {
            return false;
        }
        if( count($allowExt)==0 )
        {
            $allowExt = $this->config['allowedExt'];
        }
        return in_array( strtolower($this->extension), $allowExt);
    }

    /**
     * HasFile : check if the upload file extension is allowed
     * @return bool
     */
    public function HasFile() : bool
    {
        return !empty($this->file);
    }

    /**
     * GetFileExt : get file extension
     */
    private function _getExt()
    {
        $pathNode = explode('.', $this->file['name']);
        $nodeLen  = count($pathNode);
        if( $nodeLen==1 )
        {
            $this->extension = '';
        }
        $this->extension = $pathNode[$nodeLen-1];
    }

    /**
     * FileExt : return file extension
     * @return null
     */
    public function GetExt()
    {
        return $this->extension;
    }

    /**
     * Attr
     * @return array
     */
    public function Attr() : array
    {
        return $this->file;
    }

    /**
     * SetNewName
     * @param string|null $name
     * @return $this
     */
    public function SetNewName(string $name='')
    {
        if( !empty($name) )
        {
            $this->newFileName = $name;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function GetNewName() : string
    {
        return $this->_newFileName;
    }

    /**
     * @return string
     */
    public function GetTmpName() : string
    {
        return $this->file['tmp_name'];
    }

    /**
     * @return string
     */
    public function GetName() : string
    {
        return $this->file['name'];
    }

    /**
     * @return string
     */
    public function GetFilePath() : string
    {
        return $this->_fileStorePath;
    }

    /**
     * SetNewName
     * @param  int $name
     * @return bool
     */
    public function IsSizeAllowed(int $maxSize=0) : bool
    {
        $maxSize = ($maxSize==0) ? $this->config['maxSize'] : $maxSize;
        if( $this->file['size']>$maxSize )
        {
            return false;
        }
        return true;
    }

    /**
     * @param string $savePath
     * @return string
     */
    public function _setFilePathName(string $savePath = '') : string
    {
        if( empty($this->_newFileName) )
        {
            $this->_newFileName = Unique::Generate().'.'.$this->extension;
        }
//        $savePath = empty($savePath) ? $this->config['savePath'] : $savePath;
//        if( !is_dir($savePath) )
//        {
//            if( !mkdir($savePath) )
//            {
//                $this->_lastErrorMsg = "创建文件上传路径（{$savePath}）失败";
//            }
//        }
//        $this->_fileStorePath = $savePath.$this->_newFileName;
//        return $this->_fileStorePath;
        $this->_fileStorePath = $this->file['tmp_name'];
        return $this->_fileStorePath;
    }

    /**
     * Save :save upload file
     * @param string $savePath
     * @return bool
     */
    public function Save(string $savePath = '' ) : bool
    {
//        $newFilePath = $this->_setFilePathName($savePath);
//        if( empty($this->file['tmp_name']) )
//        {
//            $this->_lastErrorMsg = '请检查 php.ini 的post_max_size或upload_max_filesize参数，或nginx的client_max_body_size';
//        }
//        return move_uploaded_file( $this->file['tmp_name'], $newFilePath);

        $this->_setFilePathName($savePath);
        return true;
    }


    /**
     * Save :save upload file
     * @param string $savePath
     * @return bool
     */
    public function GetLastErrorMsg() : string
    {
        return $this->_lastErrorMsg;
    }


}