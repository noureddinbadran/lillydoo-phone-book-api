<?php


namespace App\Helpers\Exceptions;


use Throwable;

class UserException extends \Exception
{
    private $key;

    public function __construct($message = "", $key = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }
}