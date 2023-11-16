<?php

namespace App\Form\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ObjectiveBadGapTemperatureException extends HttpException
{

    public function __construct($ErrorMessage)
    {
        $message = "Mauvaise valeur de l'écart de température. \n" . $ErrorMessage;
        parent::__construct(400, $message);
    }

}