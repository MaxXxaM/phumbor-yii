<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 31.05.17
 * Time: 15:52
 */

namespace PhumborYii;

/**
 * Class ImageUploader
 *
 * @property Phumbor $phumbor
 * @property string $prefixServer
 *
 * @package PhumborYii
 */
class ImageUploader
{
    public $phumbor;

    private $prefixServer = '/image';

    public function __construct($phumbor)
    {
        $this->phumbor = $phumbor;
        if ($this->phumbor->uploadServer === null){
            $this->phumbor->uploadServer = $this->phumbor->server;
        }
    }

    /**
     * Return upload server URL
     * @return string
     */
    public function getUrl(){
        return $this->phumbor->uploadServer . $this->prefixServer;
    }

    /**
     * Return upload server URL
     * @return string
     */
    public function getFullUrl($fileName){
        return $this->phumbor->server . $fileName;
    }

    /**
     * Upload image to Thumbor
     * @param $file
     * @return bool|string
     */
    public function uploadImage($file){
        if ($this->phumbor->defaultFileName !== null){
            $file['name'] = $this->phumbor->defaultFileName;
        }
        $curl = new Curl($this->getUrl());
        $curl->setPostFile(Curl::getCurlFile($file));
        if (!$curl->exec()){
            return false;
        }
        return $this->getFullUrl($curl->getCreatedFileName());
    }

    /**
     * Remove image from Thumbor file storage
     * @param $fileName
     * @return bool
     */
    public function removeImage($fileName){
        $curl = new Curl($this->getFileUrl($fileName));
        $curl->setRequestDelete();
        $curl->exec();
        return $curl->isDeleted();
    }

    /**
     * Get default file name or return name
     * @param $file
     */
    private function getFileName($fileName){
        if ($this->phumbor->defaultFileName !== null){
            return $fileName . '/' . $this->phumbor->defaultFileName;
        }
        return $fileName;
    }

    public function getFileUrl($fileName){
        return $this->getUrl() . '/' . $this->getFileName($fileName);
    }

}