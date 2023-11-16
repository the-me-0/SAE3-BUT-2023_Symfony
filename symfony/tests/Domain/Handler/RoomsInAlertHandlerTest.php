<?php

use PHPUnit\Framework\TestCase;

use App\Domain\Query\RoomsInAlertQuery;
use App\Domain\Query\RoomsInAlertHandler;

use App\Entity\Room;
use App\Entity\Sensor;
use App\Entity\Objective;

class RoomsInAlertHandlerTest extends TestCase
{
    private $alertService;
    private $query;

    protected function setUp() : void
    {
        $this->query = $this->createQuery();
    }

    private function createQuery()
    {


        $rooms = array();
        foreach(['1', '2', '3', '4', '5'] as $number)
        {
            $rooms[] = $this->createRoomAndUtils("Room" . $number, "sensor" . $number);
        }
        $this->query = new RoomsInAlertQuery($rooms);
    }

    private function createRoomAndUtils(String $roomName, String $sensorName)
    {
        // Create an objective
        $objective = new Objective(
            $temperature = 20,
            $humidity = 30,
            $eCO2 = 400,
            $gapTemperature = 2,
            $gapHumidity = 10,
            $gapECO2 = 50
        );
        // Create a room
        $room = new Room(
            $surface = 20,
            $nb_windows = 5,
            $facility = null,
            $sensor = null,
            $objective = $objective,
            $name = $roomName,
            $floor = 1
        );
        // Create a sensor
        $sensor = new Sensor(
            $num = $sensorName,
            $room = $room,
            $description = "Un système d'acquisition de test",
            $enabled = true,
            $tag = 1
        );

        return $room;
    }
    public function testAlertWithGoodValues()
    {
        // A room with a sensor values equal to objective
        // TODO (Waiting for the refactor of roomsInAlertHandler)
    }

}
