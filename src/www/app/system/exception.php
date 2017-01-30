<?php

namespace App\System;

/**
 * Класс  исключения 
 */
class Exception extends \Exception
{

    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

}
