<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @return void
     */
    public function __construct($message = 'Product not found')
    {
        parent::__construct($message);
    }
}

