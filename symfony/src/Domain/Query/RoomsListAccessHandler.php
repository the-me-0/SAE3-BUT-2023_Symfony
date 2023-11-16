<?php

namespace App\Domain\Query;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Entity\User;
use App\Domain\Query\RoomsListAccessQuery;
use App\Repository\SensorRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomsListAccessHandler{


    public function __construct(    )
    {
    }

    public function handle(RoomsListAccessQuery $roomsListAccessQuery)
    {
        // Initialisation
        $user = $roomsListAccessQuery->getUser();
        $roomsList = $roomsListAccessQuery->getRooms();
        $roomsAccessList = array();
        
        // If the room is private, check if the user is in the owner collection else insert into the array
        foreach($roomsList as $room){
            if($room->isPrivate()){
                if($room->getOwner()->contains($user)){
                    array_push($roomsAccessList, $room);
                }
            }
            else{
                array_push($roomsAccessList, $room);
            }
        }

        return $roomsAccessList;
    }
}