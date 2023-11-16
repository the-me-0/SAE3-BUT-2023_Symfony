<?php

namespace App\Form\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ObjectiveBadGapHumidityException extends HttpException
{

    public function __construct($ErrorMessage)
    {
        $message = "Mauvaise valeur d'écart de l'humidité. \n" . $ErrorMessage;
        parent::__construct(400, $message);
    }

}