<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 31.05.17
 * Time: 15:52
 */

namespace PhumborYii;


class ImageUploader
{

    public static function sendRequest(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);

    }
}