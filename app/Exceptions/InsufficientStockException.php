<?php
// InsufficientStockException.php
namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct($message = 'There is not enough stock available')
    {
        parent::__construct($message);
    }
}
