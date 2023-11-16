<?php

namespace App\Domain;

use App\Entity\Room;

class Datum
{
    public function __construct(
        private Room $room,
        private string $date,
        private string $name,
        private string $desc,
        private float $value
    )
    {}


    /* ~~~~~~~~~~~~~~~~~~ Getters ~~~~~~~~~~~~~~~~~~ */
    
    public function getValue(): float
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }
}
