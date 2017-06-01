<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 01.06.17
 * Time: 14:09
 */

namespace PhumborYii\exceptions;

use Throwable;

class MethodNotAllowedException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = 'Method Not Allowed (if thumbor’s configuration disallows putting images)';
        $this->code = 405;
    }

}