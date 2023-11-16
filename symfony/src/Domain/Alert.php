<?php
namespace App\Domain;

use DateInterval;
use Symfony\Component\Validator\Constraints\Type;
use Doctrine\Common\Util\Debug;


class Alert
{
    private $duration; // duration (hour)
    
    public function __construct(private $type, private $mesured, private $objective, private $position, private $dateDeb=null, private $dateFin=null)
    {
        /*
        * $type: temp, hum, co2 / It's the type of the alert
        * $mesured: float / It's the mesured value from the sensor
        * $objective: float / It's the objective value from the room
        * $position: higher or lower / If is the value is higher or lower than the objective
        * $dateDeb : DateTime / It's the date when the alert start
        $ $dateFin : DateTime / It's the date when the alert end
        */
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMesured(): float
    {
        return $this->mesured;
    }
    public function getObjective(): float
    {
        return $this->objective;
    }

    public function getDifference(): float
    {
        return $this->mesured - $this->objective;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getDateDeb(): \DateTime
    {
        return $this->dateDeb;
    }

    public function getDateFin(): \DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function calculateDuration() : void
    {
        /* 
         * Calculate the duration of the alert
        */

        $interval = $this->dateDeb->diff($this->dateFin);
        $days = $interval->format('%a');
        $hour = $interval->format('%H');
        $minutes = $interval->format('%i');
        $this->duration = new DateInterval('PT' . $days*24 + $hour. 'H' . $minutes . 'M');
    }

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }

    public function getAlertMessage(): string
    {
        /* 
         * Return the message of the alert
        */
        
        switch ($this->type) {
            case "temp":
                if($this->getPosition() == "higher")
                    $message = "La température est trop haute de " . $this->getDifference() . "°C";
                elseif ($this->getPosition() == "lower")
                    $message = "La température est trop basse de " . $this->getDifference() . "°C";
                break;
            case "hum":
                if ($this->getPosition() == "higher")
                    $message = "L'humidité est trop haute de " . $this->getDifference() . "%";
                elseif ($this->getPosition() == "lower")
                    $message = "L'humidité est trop basse de " . $this->getDifference() . "%";
                break;
            case "co2":
                $message = "Le ECO2 est trop élevé de " . $this->getDifference() . "ppm";
                break;
        }

        return $message;
    }
}