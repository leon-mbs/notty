<?php

namespace App;

/**
 * Класс  исключения 
 */
class Exception extends \Exception
{

    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

}
