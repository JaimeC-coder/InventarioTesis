<?php

namespace App\Exceptions;

use Exception;

class PurchaseStatusLockedException extends Exception
{
    public function __construct(string $message = 'No se puede modificar el estado de una compra que ya fue recibida.')
    {
        parent::__construct($message);
    }
}
