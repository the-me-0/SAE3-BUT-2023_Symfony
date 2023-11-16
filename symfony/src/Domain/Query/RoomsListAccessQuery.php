<?php

namespace App\Domain\Query;

use App\Entity\User;


class RoomsListAccessQuery{

    public function __construct(private $rooms, private User $user)
    {
        
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRooms()
    {
        return $this->rooms;
    }
}