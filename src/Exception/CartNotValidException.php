<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class CartNotValidException extends \Exception
{
    public function __construct()
    {
        parent::__construct('The cart could not be submitted due to errors');
    }
}
