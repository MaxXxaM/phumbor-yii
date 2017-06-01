<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 01.06.17
 * Time: 14:09
 */

namespace PhumborYii\exceptions;

use Throwable;

class NotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = 'Not Found (image doesn’t exists)';
        $this->code = 404;
    }

}