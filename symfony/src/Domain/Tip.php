<?php

namespace App\Domain;

class Tip
{
    /* 
        The differents types are :
            - cool
            - heat
            - ventilate
            - humidify
            - closeWindows

    */

    private $type;
    private $message;
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage() : string
    {
        return $this->message;
    }
}