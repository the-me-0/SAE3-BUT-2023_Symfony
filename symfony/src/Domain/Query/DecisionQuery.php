<?php


namespace App\Domain\Query;


class DecisionQuery
{
    private $meteo;
    private $alerts;

    public function __construct($meteo, array $alerts)
    {
        /*
         * $meteo: Meteo / It's the meteo object
         * $alerts: array / It's the alerts array
        */
        $this->meteo = $this->meteoToArray($meteo);
        $this->alerts = $alerts;
    }

    // function that returns true
    public function getAlerts():array
    {
        return $this->alerts;
    }

    public function getMeteo():array
    {
        return $this->meteo;
    }

    private function meteoToArray($meteo):array
    {
        /*
         * $meteo: Meteo / It's the meteo object
         * return: array / It's the meteo object in array
        */

        return array(
            "temp" => $meteo->getTemp(),
            "hum" => $meteo->getHum(),
            "co2" => $meteo->getCo2()
        );
    }


    public function getAllDatas() : array
    {
        /*
         * return: array / It's the meteo object and the alerts in array
        */
        return array(
            'alerts' => $this->alerts,
            'meteo' => $this->meteo
        );
    }
}