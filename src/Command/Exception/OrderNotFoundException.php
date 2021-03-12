<?php


namespace App\Command\Exception;


use Throwable;

class OrderNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Order not found', 10);
    }
}