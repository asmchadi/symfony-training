<?php

declare(strict_types=1);

namespace App\Command\Exception;

use Throwable;

class NoOrdersException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No orders were set', 10);
    }
}