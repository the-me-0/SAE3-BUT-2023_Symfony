<?php

namespace App\Form\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValueNotChangeException extends HttpException
{

    public function __construct(string $plus = '')
    {
        $message = "Aucune valeur n'a été modifiée. \n" . $plus;
        parent::__construct(400, $message);
    }

}