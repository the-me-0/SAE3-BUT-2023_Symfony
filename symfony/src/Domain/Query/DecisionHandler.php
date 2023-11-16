<?php

namespace App\Domain\Query;

use App\Domain\Tip;

class DecisionHandler
{
    private $query;
    private $todo = array();
    private $prohibition = array();
    public function __construct(DecisionQuery $query)
    {
        /* 
         * $query: DecisionQuery / It's the query object
         */

        $this->query = $query;
    }

    public function handle()
    {
        /* 
         * return: array / It's the tips array
         */

        // Get the alerts and the meteo in arrays
        $alerts = $this->query->getAlerts();
        $meteo = $this->query->getMeteo();

        
        foreach ($alerts as $alert) {
            if ($alert->getType() == "temp") {
                if ($alert->getPosition() == "higher") {
                    $this->todo[] = new Tip("cool", "Il faut refroidir la salle."); // If it's a temperature alert and it's higher than the objective
                    if ($meteo['temp'] > $alert->getMesured())
                        $this->todo[] = new Tip("closeWindows", "Il faut éviter d'ouvrir les fenêtres, la température extérieur est plus haute."); // If the temperature outside is higher than inside
                } elseif ($alert->getPosition() == "lower") {
                    $this->todo[] = new Tip("heat", "Il faut réchauffer la salle."); // If it's a temperature alert and it's lower than the objective
                    if ($meteo['temp'] < $alert->getMesured())
                        $this->todo[] = new Tip("closeWindows", "Il faut éviter d'ouvrir les fenêtres, la température extérieur est plus basse."); // If the temperature outside is lower than inside
                }

            }

            if ($alert->getType() == "hum") {
                if ($alert->getPosition() == "higher") {
                    $this->todo[] = new Tip("dehumidify", "Il faut baisser l'humidité de la salle."); // If it's a humidity alert and it's higher than the objective
                    if ($meteo['hum'] > $alert->getMesured())
                        $this->todo[] = new Tip("closeWindows", "Il faut éviter d'ouvrir les fenêtres, l'humidité extérieur est plus haute."); // If the humidity outside is higher than inside
                } elseif ($alert->getPosition() == "lower") {
                    $this->todo[] = new Tip("humidify", "Il faut augmenter l'humidité de la salle."); // If it's a humidity alert and it's lower than the objective
                    if ($meteo['hum'] < $alert->getMesured())
                        $this->todo[] = new Tip("closeWindows", "Il faut éviter d'ouvrir les fenêtres, l'humidité est plus basse à l'extérieur."); // If the humidity outside is lower than inside
                }
            }

            if ($alert->getType() == "co2") {
                if ($alert->getPosition() == "higher") {
                    $this->todo[] = new Tip("ventilate", "Il faut aérer la salle."); // If it's a co2 alert and it's higher than the objective
                    if ($meteo['co2'] > $alert->getMesured())
                        $this->todo[] = new Tip("closeWindows", "Il faut éviter d'ouvrir les fenêtres, le eCO2 est plus haut à l'extérieur."); // If the co2 outside is higher than inside
                }
            }
        }
        return $this->todo;
    }
}