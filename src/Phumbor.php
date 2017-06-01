<?php

/**
 * Created by PhpStorm.
 * User: maxxxam
 * Date: 31.05.17
 * Time: 15:33
 */

namespace PhumborYii;

use Thumbor\Url\Builder;
use yii\base\Component;

class Phumbor extends Component
{

    public $server = 'http://localhost:8000';
    public $uploadServer;
    public $defaultFileName;
    public $secret;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @param $source
     * @return Builder
     */
    public function getUrlBuilder($source, $uploadServer = false){
        return Builder::construct($uploadServer ? $this->uploadServer : $this->server, $this->secret, $source);
    }

    /**
     * Return full url by hash
     * @param $fileName
     * @return string
     */
    public function getFullUrl($fileName){
        $uploader = new ImageUploader($this);
        return $uploader->getFileUrl($fileName);
    }

    /**
     * Upload image to Thumbor by $_FILE data
     * @param $file
     * @return bool|string
     */
    public function upload($file){
        $uploader = new ImageUploader($this);
        return $uploader->uploadImage($file);
    }

    /**
     * Remove image from Thumbor by hash (if set default fileName) OR full name - <hash>/fileName.png
     * @param $fileName
     * @return bool
     */
    public function remove($fileName){
        $uploader = new ImageUploader($this);
        return $uploader->removeImage($fileName);
    }

    /**
     * Return id file by url
     * @param $url
     * @return bool
     */
    public function getImageId($url){
        if (preg_match('/\/([a-z0-9]{32})\//', $url, $matches)) {
            return $matches[1];
        }
        return false;
    }

}