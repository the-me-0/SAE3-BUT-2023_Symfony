<?php

namespace App\Form\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ObjectiveBadGapECO2Exception extends HttpException
{

    public function __construct($ErrorMessage)
    {
        $message = "Mauvaise valeur d'écart du eCO2. \n" . $ErrorMessage;
        parent::__construct(400, $message);
    }

}