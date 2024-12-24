<?php

namespace App\Exceptions;

use App\Http\Responses\Response;
use Exception;

class InvalidQuantitiesException extends Exception
{
   
    protected $invalidQuantities;
    protected $customMessage;

    public function __construct($invalidQuantities, $customMessage = null)
    {
        parent::__construct($customMessage ?? 'Invalid quantities entered for the following items');
        $this->invalidQuantities = $invalidQuantities;
        $this->customMessage = $customMessage;
    }

    public function getInvalidQuantities()
    {
        return $this->invalidQuantities;
    }

    public function render()
    {
        $message = $this->customMessage ?? $this->getMessage();
        return Response::Error($this->getInvalidQuantities(), $message);
    }

}
