<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 31.05.17
 * Time: 15:52
 */

namespace PhumborYii;


use PhumborYii\exceptions\CurlRequestException;
use PhumborYii\exceptions\NotFoundException;
use PhumborYii\exceptions\MethodNotAllowedException;
use PhumborYii\exceptions\PreconditionFailedException;
use PhumborYii\exceptions\UnsupportedMediaTypeException;

class Curl
{

    private $curl;
    private $_response;
    private $_httpCode;

    public function __construct($url)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        return $this->curl;
    }

    public function setPostData($data)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
    }

    public function setRequestDelete()
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    }

    public function setPostFile($curlFile)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, ['media' => $curlFile]);
    }

    public function exec()
    {
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($this->curl, CURLOPT_STDERR, $verbose);

        $this->_response = curl_exec($this->curl);

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);

        $this->_httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->throwError();

        if ($this->_response === false) {
            $errorCode = curl_errno($this->curl);
            $error = curl_error($this->curl);
            throw new CurlRequestException($error, $errorCode);
            curl_close($this->curl);
            return false;
        }
        curl_close($this->curl);
        return true;
    }

    private function throwError(){
        switch ($this->_httpCode){
            case 404:
                throw new NotFoundException();
                break;
            case 405:
                throw new MethodNotAllowedException();
                break;
            case 412:
                throw new PreconditionFailedException();
                break;
            case 415:
                throw new UnsupportedMediaTypeException();
                break;
        }
    }

    /**
     * Return created file name
     * @return bool|string
     */
    public function getCreatedFileName(){
        if ($this->_httpCode === 201){
            return $this->getHeader('Location');
        } else {
            return false;
        }
    }

    /**
     * Check deleted file before exec
     * @return bool
     */
    public function isDeleted(){
        if ($this->_httpCode === 204){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return CURL Object File by POST $_FILE
     * @param $file
     * @return \CurlFile
     */
    public static function getCurlFile($file){
        return new \CurlFile($file['tmp_name'], $file['type'], $file['name']);
    }

    /**
     * Return needed header by name
     * @param $name
     * @return bool|string
     */
    private function getHeader($name){
        $headers = $this->getHeaders();
        return isset($headers[$name]) ? $headers[$name] : false;
    }

    /**
     * Return headers from response
     * @return array|bool
     */
    private function getHeaders(){
        if (!preg_match_all('/([A-Za-z\-]{1,})\:(.*)\\r/', $this->_response, $matches) || !isset($matches[1], $matches[2])){
            return false;
        }

        $headers = [];

        foreach ($matches[1] as $index => $key){
            $headers[trim($key)] = trim($matches[2][$index]);
        }

        return $headers;
    }

}