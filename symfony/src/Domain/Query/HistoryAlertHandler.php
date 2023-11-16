<?php

namespace App\Domain\Query;

use App\Domain\Query\DataQuery;
use App\Domain\Query\DataHandler;
use App\Domain\Query\HistoryAlertQuery;
use App\Domain\Alert;
use Symfony\Component\VarDumper\VarDumper;
use Doctrine\Common\Util\Debug;
use App\Domain\Datum;
use DateInterval;


use App\Entity\Room;
use Symfony\Component\Validator\Constraints\DateTime;


class HistoryAlertHandler{
    private DateInterval $tempHour;
    private DateInterval $co2Hour;
    private DateInterval $humidityHour;
    private $score;
    private $tempAlert;
    private $co2Alert;
    private $humidityAlert;
    private $tempData;
    private $co2Data;
    private $humidityData;
    private $historyAlertQuery;

    public function __construct(
        private DataHandler $dataHandler,
    ){
        $this->tempAlert = array();
        $this->co2Alert = array();
        $this->humidityAlert = array();
       
    }

    // extract all the data from the API between two date
    public function getData(){
        // Data extraction from the API database
        $this->tempData = $this->dataHandler->handle(new DataQuery($this->historyAlertQuery->getRoom()->getSensor()->getTag(), "temp", 0, $this->historyAlertQuery->getDateDeb()->format('Y-m-d'), $this->historyAlertQuery->getDateFin()->format('Y-m-d')));
        $this->co2Data = $this->dataHandler->handle(new DataQuery($this->historyAlertQuery->getRoom()->getSensor()->getTag(), "co2", 0, $this->historyAlertQuery->getDateDeb()->format('Y-m-d'), $this->historyAlertQuery->getDateFin()->format('Y-m-d')));
        $this->humidityData = $this->dataHandler->handle(new DataQuery($this->historyAlertQuery->getRoom()->getSensor()->getTag(), "hum", 0, $this->historyAlertQuery->getDateDeb()->format('Y-m-d'), $this->historyAlertQuery->getDateFin()->format('Y-m-d')));
    }   

    // data : a data from the API (DATUM TYPE OBJECT)
    // type : temp, co2, hum
    public function checkIfAlert(Datum $data, $type): bool
    {
        switch($type){
            case "temp":
                if(
                    $data->getValue() > $this->historyAlertQuery->getRoom()->getObjective()->getTemperature() + $this->historyAlertQuery->getRoom()->getObjective()->getGapTemperature() or
                    $data->getValue() < $this->historyAlertQuery->getRoom()->getObjective()->getTemperature() - $this->historyAlertQuery->getRoom()->getObjective()->getGapTemperature()
                ){
                    return true;
                } else {
                    return false;
                }
            case "co2":
                if(
                    $data->getValue() > $this->historyAlertQuery->getRoom()->getObjective()->getECo2() + $this->historyAlertQuery->getRoom()->getObjective()->getGapECo2() or
                    $data->getValue() < $this->historyAlertQuery->getRoom()->getObjective()->getECo2() - $this->historyAlertQuery->getRoom()->getObjective()->getGapECo2()
                ){
                    return true;
                } else {
                    return false;
                }
            case "hum":
                if(
                    $data->getValue() > $this->historyAlertQuery->getRoom()->getObjective()->getHumidity() + $this->historyAlertQuery->getRoom()->getObjective()->getGapHumidity() or
                    $data->getValue() < $this->historyAlertQuery->getRoom()->getObjective()->getHumidity() - $this->historyAlertQuery->getRoom()->getObjective()->getGapHumidity()
                ){
                    return true;
                } else {
                    return false;
                }
            default:
                return false;
        }
    }

    // dataList : array of Datum
    // type : temp, co2, hum
    // return : array of Alert
    public function calculateHistoryAlert($dataList, $type){
        $lastDataWasInAlert = false;
        $alertList = array();

        foreach ($dataList as $data) {
            // Check if we are actually in alert --> Resume in a function which calculate
            if($this->checkIfAlert($data, $type)){
                // If we were not in alert before, we create a new alert
                if (!$lastDataWasInAlert) {
                    array_push($alertList, new Alert($type, $data->getValue(), $this->historyAlertQuery->getRoom()->getObjective()->getTemperature(), "null", new \DateTime($data->getDate()), new \DateTime($data->getDate())));
                    $lastDataWasInAlert = true;
                // If we were in alert before, we just update the end date of the alert
                } else {
                    end($alertList)->setDateFin(new \DateTime($data->getDate()));
                }
            // If we are not in alert
            }else{
                // If we were in alert before, we set the lastDataWasInAlert to false                
                if(end($alertList) && $lastDataWasInAlert)
                    end($alertList)->setDateFin(new \DateTime($data->getDate()));
                $lastDataWasInAlert = false;
            }
        }
        return $alertList;
    } 

    // alertList : array of Alert
    // type : temp, co2, hum
    // return : a duration on Hour
    public function calculateAlertDuration($alertList) : DateInterval{
        $sum = new DateInterval('PT0H0M');
        foreach ($alertList as $alert){
            $alert->calculateDuration();
            $sum->h += $alert->getDuration()->h;
            $sum->i += $alert->getDuration()->i;
        }
        return $sum;
    }
    
    public function calculateScore(){
        $dateDeb = $this->historyAlertQuery->getDateDeb();
        $dateFin = $this->historyAlertQuery->getDateFin();

        $totalTime = $dateDeb->diff($dateFin);
        $days = $totalTime->format('%a');
        $hour = $totalTime->format('%h');
        $minutes = $totalTime->format('%i');
        $totalTime = $days * 24 + $hour + $minutes / 60;
        
        $avarage = $this->tempHour->h + $this->co2Hour->h + $this->humidityHour->h + $this->tempHour->i / 60 + $this->co2Hour->i / 60 + $this->humidityHour->i / 60;
        $avarage = $avarage / ($totalTime * 3) * 100;
        return round(100 - $avarage, 2);
    }
    
    public function handle(HistoryAlertQuery $historyAlertQuery)
    {
        $this->historyAlertQuery = $historyAlertQuery;
        // data extraction
        $this->getData();
        // calculate the alerts
        $this->tempAlert = $this->calculateHistoryAlert($this->tempData, 'temp');
        $this->co2Alert = $this->calculateHistoryAlert($this->co2Data, 'co2');
        $this->humidityAlert = $this->calculateHistoryAlert($this->humidityData, 'hum');
        // calculate the duration of the rooms
        $this->tempHour = $this->calculateAlertDuration($this->tempAlert);
        $this->co2Hour = $this->calculateAlertDuration($this->co2Alert);
        $this->humidityHour = $this->calculateAlertDuration($this->humidityAlert);

        // calculate the score of the room
        $this->score = $this->calculateScore();
    }

    public function getTempHour()
    {
        return $this->tempHour;
    }

    public function getECO2Hour()
    {
        return $this->co2Hour;
    }

    public function getHumidityHour()
    {
        return $this->humidityHour;
    }

    public function getTempAlert()
    {
        return $this->tempAlert;
    }

    public function getECO2Alert()
    {
        return $this->co2Alert;
    }

    public function getHumidityAlert()
    {
        return $this->humidityAlert;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function getTempData(){
        return $this->tempData;
    }
}