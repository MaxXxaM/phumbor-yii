<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 01.06.17
 * Time: 14:09
 */

namespace PhumborYii\exceptions;

use Throwable;

class PreconditionFailedException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = 'Precondition Failed (image is too small or the file is not an image)';
        $this->code = 412;
    }

}