<?php

namespace App\Domain\Query;

use App\Entity\Room;

use Doctrine\Common\Util\Debug;


class HistoryAlertQuery
{
    public function __construct(
        private \DateTime $dateDeb,
        private \DateTime $dateFin,
        private $room,
    )
    {

    }

    public function getDateDeb()
    {
        return $this->dateDeb;
    }

    public function getDateFin()
    {
        return $this->dateFin;
    }

    public function getRoom()
    {
        return $this->room;
    }
}