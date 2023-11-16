<?php

namespace App\Domain\Query;

use App\Domain\Datum;
use App\Domain\DataProvider;
use App\Repository\SensorRepository;
use App\Domain\Alert;
use App\Entity\Sensor;
use App\Entity\Objective;
use App\Entity\Room;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Util\Debug;

use DateTimeZone;
use DateTime;

class RoomsInAlertHandler
{
    private SensorRepository $sensorRepository;

    public function __construct(
        private ManagerRegistry $registry,
    )
    {
        /* 
         * $registry: ManagerRegistry / It's the registry of the database
        */
        $this->sensorRepository = $registry->getRepository(Sensor::class);
    }


    public function handle(RoomsInAlertQuery $query, array $datumArray): array
    {
        /* Initialise the data array */
        $rooms_in_alert = [];
<<<<<<< HEAD
        // If there is only one room in rooms, we use getAlerts
        if (count($rooms) === 1) {
            $rooms_in_alert = $this->getAlerts($rooms[0]);
        }
        else if($rooms !== null)
        {
            $rooms_in_alert = $this->getListAlertedRooms($rooms);
        }

        return $rooms_in_alert;

    }

    private function getValues($room)
    {
        $objective = $room->getObjective();
        if ($objective === null) {
            $facility = $room->getFacility();
            $objective = $facility->getObjective();
        }

        /* definitions needed by DataHandler */
        $datum = array();
        $sensor = $this->sensorRepository->findByRoom($room->getId());

        return ["objective" => $objective, $sensor];
    }
    public function getAlerts($room)
    {
        $objective = new GetRoomObjectiveHandler(new GetRoomObjectiveQuery($room));
        $objective = $objective->handle();

        if($room->getSensor() === null) {
            return [];
        } else {
            $provided = $this->getProvided($room->getSensor());
        }


        $alerts = [];

        // first, we check if we managed to retrieve data from the sensor (via API)
        if($provided['temp'] == null || $provided['hum'] == null || $provided['co2'] == null) {

            // we didn't manage to retrieve data from the sensor
            // we could return an error message if the sensor is not responding since a long time (to implement)
            return [];
        }

        // verify if the temperature of the room has a difference of 2 or more degrees with the objective
        if (abs($provided['temp'][0]->getValue() - $objective->getTemperature()) >= 2) {
            $alerts[] = new Alert("temp", $provided['temp'][0]->getValue(), $objective->getTemperature());
        }
        // verify if the humidity of the room has a difference of 10 or more percent with the objective
        if (abs($provided['hum'][0]->getValue() - $objective->getHumidity()) >= 10) {
            $alerts[] = new Alert("hum", $provided['hum'][0]->getValue(), $objective->getHumidity());
        }
        // verify if the CO2 of the room has a difference of 200 or more ppm with the objective
        if (abs($provided['co2'][0]->getValue() - $objective->getECO2()) >= 200) {
            $alerts[] = new Alert("co2", $provided['co2'][0]->getValue(), $objective->getECO2());
        }
        return $alerts;
    }

    private function getProvided(Sensor $sensor)
    {

        $dataProvider = new DataProvider();
        $query = new DataQuery($sensor->getTag(), 'temp', 1);
        $dataHandler = new DataHandler($dataProvider, $this->sensorRepository); 
        
        $provided['temp'] = $dataHandler->handle($query);

        $query->setName('hum');
        $provided['hum']  = $dataHandler->handle($query);

        $query->setName('co2');
        $provided['co2']  = $dataHandler->handle($query);

        return $provided;
=======
        
        /* Get all the rooms to check */
        $rooms = $query->getRooms();


        /* Safeguard */
        if($rooms !== null)
        {
            // If there is only one room, we use getListAlertedRoom
            if(count($rooms) == 1) {
                $rooms_in_alert = $this->getListAlertedRoom($rooms[0], $datumArray);
            } else {
                // If there are multiple rooms, we use getListAlertedRooms
                $rooms_in_alert = $this->getListAlertedRooms($rooms, $datumArray);
            }
        }

        /* Return the rooms in alert */
        return $rooms_in_alert;
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd
    }

    /* This function returns an array of alerted rooms, like : */
    /* [{"room" => RoomEntity, "alerts" => [AlertEntity, AlertEntity, AlertEntity]},{"room" => RoomEntity, "alerts" => [AlertEntity]}] */
    private function getListAlertedRooms($rooms, $datumArray)
    {

        /* Initialise the data array */
        $rooms_in_alert = [];

        /* Check the status of each room */
        foreach ($rooms as $room) {

            // " Les mots sont le support de la compréhension et pour ceux qui les écouteront l'énonciation de la vérité. " - V
            
            /* get the sensor of the room */
            /* Safeguard used by Dashboard controller */
            $sensor = null;
            if ($room != []) $sensor = $this->sensorRepository->findByRoom($room->getId());

<<<<<<< HEAD
            if ($sensor !== null) {
                $provided = [];
                $provided = $this->getProvided($sensor);
=======
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd

            /* Safeguard */
            if ($sensor != null) {

                $datums = array();
                foreach ($datumArray as $datum) {
                    if ($datum->getRoom() == $room) {
                        $datums[] = $datum;
                    }
                }

                /* Get the objective of the room */
                $objective = $room->getObjective();

                /* Get the alerts for the room */
                $alerts = $this->getAlerts($room, $sensor, $objective, $datums);

                /* If there are alerts, we add the room to the list of alerted rooms */
                if($alerts !== []) {
                    $rooms_in_alert[] = array(
                        "room" => $room,
                        "alerts" => $alerts
                    );
                }
            }
        }

        /* Return the rooms in alert */
        return $rooms_in_alert;
    }

    /* This function is a variation of getListAlertedRooms, used to returns alerts on a specific room */
    /* It returns an array of alerts, without specifying the room */
    private function getListAlertedRoom($room, $datumArray)
    {
        /* Initialise the data array */
        $rooms_in_alert = [];

        /* get the sensor of the room */
        $sensor = $this->sensorRepository->findByRoom($room->getId());

        /* Safeguard */
        if ($sensor != null) {

            /* Get the objective of the room */
            $objective = $room->getObjective();

            /* Safeguard */
            /* Verify that $datumArray has Datum inside */
            if (isset($datumArray[$sensor->getNum()]))
                $datumArray = $datumArray[$sensor->getNum()];

            /* Get the alerts for the room */
            $alerts = $this->getAlerts($room, $sensor, $objective, $datumArray);

            /* If there are alerts, we add the room to the list of alerted rooms */
            if($alerts !== []) {
                $rooms_in_alert = $alerts;
            }
        }
    
        /* Return the rooms in alert */
        return $rooms_in_alert;
    }

    
    /** @return Alert[] */
    public function getAlerts(Room $room, Sensor $sensor, Objective $objective, array $datums)
    {

        /* Get the data from the concerned sensor */
        $provided = ["temp" => null, "hum" => null, "co2" => null];
        foreach ($datums as $datum) {
            if ($datum->getName() == "temp") {
                $provided["temp"] = $datum;
            } else if ($datum->getName() == "hum") {
                    $provided["hum"] = $datum;
            } else if ($datum->getName() == "co2") {
                $provided["co2"] = $datum;
            }
        }

        /* Initialise the alerts array */
        $alerts = [];

        // First we update the sensor's state
        /* We select the provided data that is the most recent */
        $mostRecent = 0;
        if ($provided['temp'] != null && DateTime::createFromFormat('d-m-Y H:i:s', $provided['temp']->getDate())->getTimeStamp() > $mostRecent) {
            $mostRecent = DateTime::createFromFormat('d-m-Y H:i:s', $provided['temp']->getDate())->getTimeStamp();
        }
        if ($provided['hum'] != null && DateTime::createFromFormat('d-m-Y H:i:s', $provided['hum']->getDate())->getTimeStamp() > $mostRecent) {
            $mostRecent = DateTime::createFromFormat('d-m-Y H:i:s', $provided['hum']->getDate())->getTimeStamp();
        }
        if ($provided['co2'] != null && DateTime::createFromFormat('d-m-Y H:i:s', $provided['co2']->getDate())->getTimeStamp() > $mostRecent) {
            $mostRecent = DateTime::createFromFormat('d-m-Y H:i:s', $provided['co2']->getDate())->getTimeStamp();
        }
        $this->checkSensorState($room->getSensor(), $mostRecent);


        //  checks if the current temperature is lower than the objective minus the gap temperature
        if ($provided['temp'] != null && $provided['temp']->getValue() < $objective->getTemperature() - $objective->getGapTemperature()) {
            $alerts[] = new Alert("temp", $provided['temp']->getValue(), $objective->getTemperature(), "lower");
        }
        // checks if the current temperature is higher than the objective plus the gap temperature
        if ($provided['temp'] != null && $provided['temp']->getValue() > $objective->getTemperature() + $objective->getGapTemperature())
        {
            $alerts[] = new Alert("temp", $provided['temp']->getValue(), $objective->getTemperature(), "higher");
        }
        
        // checks if the current humidity is lower than the objective minus the gap humidity
        if ($provided['hum'] != null && $provided['hum']->getValue() < $objective->getHumidity() - $objective->getGapHumidity()) {
            $alerts[] = new Alert("hum", $provided['hum']->getValue(), $objective->getHumidity(), "lower");
        }
        // checks if the current humidity is higher than the objective plus the gap humidity
        if ($provided['hum'] != null && $provided['hum']->getValue() > $objective->getHumidity() + $objective->getGapHumidity()) {
            $alerts[] = new Alert("hum", $provided['hum']->getValue(), $objective->getHumidity(), "higher");
        }

        // checks if the current CO2 value is higher than the objective plus the gap CO2
        if ($provided['co2'] != null && $provided['co2']->getValue() > $objective->getECO2() + $objective->getGapECO2()) {
            $alerts[] = new Alert("co2", $provided['co2']->getValue(), $objective->getECO2(), "higher");
        }

        /* Return the alerts */
        return $alerts;
    }


    /* This function updates the state of the sensor */
    private function checkSensorState(Sensor $sensor, $lastDatumTimestamp)
    {
        $sensorStateValid = true;
        // Then, we verify if the date of the last datum is less than 30 minutes ago
        $now = new \DateTime("now", new DateTimeZone('Europe/Paris'));

        // If the last datum is older than 30 minutes, we consider the sensor as not working
        if($lastDatumTimestamp < $now->getTimestamp() - 1800) {
            $sensorStateValid = false;
        }

        // Now that we know if the sensor state is valid, we can modify it's state
        $sensor->setEnabled($sensorStateValid);

        // We save the sensor state
        $this->registry->getManager()->persist($sensor);
        $this->registry->getManager()->flush();
    }
}