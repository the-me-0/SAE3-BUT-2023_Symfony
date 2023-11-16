<?php

namespace App\Form\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ObjectiveBadHumidityException extends HttpException
{

    public function __construct($ErrorMessage)
    {
        $message = "Mauvaise valeur d'humidité. \n" . $ErrorMessage;
        parent::__construct(400, $message);
    }

}