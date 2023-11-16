<?php

namespace App\Domain\Query;

use App\Entity\Room;

class RoomsInAlertQuery
{
    public function __construct(
        private array $rooms,
    ) {}


    /* This setter allows to use the same query instead of creating a new one for the same room */
    public function setRoom($rooms):void
    {
        $this->rooms = $rooms;
    }

    /* ~~~~~~~~~~~~~~~~~~ Getters ~~~~~~~~~~~~~~~~~~ */

    public function getRooms(): array
    {
        return $this->rooms;
    }
}