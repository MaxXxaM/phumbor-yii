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
    public $secret;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getUrlBuilder($source){
        return Builder::construct($this->server, $this->secret, $source);
    }

    public function upload(){

    }

}