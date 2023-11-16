<?php

namespace App\Domain\Query;

use App\Repository\SensorRepository;
use App\Domain\DataProvider;
use App\Domain\Datum;
use App\Entity\Sensor;

use Doctrine\Persistence\ManagerRegistry;



class DataHandler
{
    private DataProvider $dataProvider;
    private SensorRepository $sensorRepository;

    /* This attribute is bound to contain all string errors returned by the DataProvider */
    private array $errors = array();


    public function __construct(
        ManagerRegistry $registry,
    )
    {
        /* Initialise the dataProvider used to get the data */
        $this->dataProvider = new DataProvider();

        /* Initialise the sensorRepository used to get the room associated with a sensor */
        $this->sensorRepository = $registry->getRepository(Sensor::class);
    }

    /* This handler is meant to return the sensor's data asked via the query */
    /** @return Datum[] */
    public function handle(DataQuery $query):iterable
    {
        /* Initialise the data array */
        $data = array();

        /* Get the room associated with the sensor */
        $room = $this->sensorRepository->getByTag($query->getTag())->getRoom();

        /* Set the room in the dataProvider */
        $this->dataProvider->setRoom($room);

        /* Get the data from the dataProvider */
        $data = $this->dataProvider->provide($query->getTag(), $query->getName(), $query->getLimit(), $query->getDate1(), $query->getDate2());

        $confirmedData = array();

        /* Safeguard */
        /* If the dataprovider had a problem with a datum, it will return a string containing the error message */
        for ($i = 0; $i < count($data); $i++) {
            if(is_string($data[$i])) {
                /* If the datum is invalid, we add the error message to the list of errors */
                /* If the error isn't already in the list we add it to the list */
                if(!in_array($data[$i], $this->errors)) {
                    $this->errors[] = $data[$i];
                }
            } else {
                /* Everything's fine, the datum seems valid */
                $confirmedData[] = $data[$i];
            }
        }

        /* Return the data */
        return $confirmedData;
    }

    /* This function is used to get the errors returned by the dataProvider */
    public function getErrors():array
    {
        return $this->errors;
    }
}
