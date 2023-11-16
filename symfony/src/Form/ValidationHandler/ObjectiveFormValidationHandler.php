<?php

namespace App\Form\ValidationHandler;

use App\Controller\FacilityController;
use App\Form\ValidationQuery\ObjectiveFormValidationQuery;
use App\Entity\Objective;

use DateTime;
use DateTimeZone;

// Handler for the validation of the objective form
class ObjectiveFormValidationHandler
{
    private $query;
    public function __construct()
    {    }

    public function handle(ObjectiveFormValidationQuery $query)
    {
        $this->query = $query;
        

        // For each room of the facility
        if ($query->getType() == "facility") {
            foreach ($query->getElement()->getRooms() as $room) {
                $objectiveRoom = $room->getObjective();

                if ($query->getApplyToAll()) {
                    // If the user wants to apply the objective to all the rooms, set the same value as facility
                    $dataCopy = [
                        'temperature' => $query->getForm()->getData()->getTemperature(),
                        'humidity' => $query->getForm()->getData()->getHumidity(),
                        'eCO2' => $query->getForm()->getData()->getECO2(),
                        'gapTemperature' => $query->getForm()->getData()->getGapTemperature(),
                        'gapHumidity' => $query->getForm()->getData()->getGapHumidity(),
                        'gapECO2' => $query->getForm()->getData()->getGapECO2(),
                        'personal' => false,
                    ];

                    $newObjective = $this->createAndRegisterObjectives($dataCopy, $room->getObjective());
                    $room->setObjective($newObjective);

                } else if ($objectiveRoom->getPersonal() == false) {
                    // if the room has not its personal objective, we set the same value as the facility
                    $dataCopy = [
                        'temperature' => $query->getForm()->getData()->getTemperature(),
                        'humidity' => $query->getForm()->getData()->getHumidity(),
                        'eCO2' => $query->getForm()->getData()->getECO2(),
                        'gapTemperature' => $query->getForm()->getData()->getGapTemperature(),
                        'gapHumidity' => $query->getForm()->getData()->getGapHumidity(),
                        'gapECO2' => $query->getForm()->getData()->getGapECO2(),
                        'personal' => false,
                    ];

                    $newObjective = $this->createAndRegisterObjectives($dataCopy, $room->getObjective());
                    $room->setObjective($newObjective);
                }
            }
        }

        // Set variables
        $oldObjective = $query->getElement()->getObjective();
        $newObjective = $this->registerObjectivesFromForm($query->getForm(), $oldObjective);
        $query->getElement()->setObjective($newObjective);


        // Flush database
        $query->getDoctrine()->getManager()->flush();

        return true;
    }

    private function registerObjectivesFromForm($form, $oldObjective): Objective
    {
        $data = [
            'temperature' => $form->getData()->getTemperature(),
            'humidity' => $form->getData()->getHumidity(),
            'eCO2' => $form->getData()->getECO2(),
            'gapTemperature' => $form->getData()->getGapTemperature(),
            'gapHumidity' => $form->getData()->getGapHumidity(),
            'gapECO2' => $form->getData()->getGapECO2(),
            'personal' => true,
        ];

        return $this->createAndRegisterObjectives($data, $oldObjective);
    }

    private function createAndRegisterObjectives(array $newData, $oldObjective)
    {
        // Create a new objective
        $newObjective = new Objective();

        // Set the values of the objective
        $newObjective->setTemperature($newData['temperature']);
        $newObjective->setHumidity($newData['humidity']);
        $newObjective->setECO2($newData['eCO2']);
        $newObjective->setGapTemperature($newData['gapTemperature']);
        $newObjective->setGapHumidity($newData['gapHumidity']);
        $newObjective->setGapECO2($newData['gapECO2']);
        $newObjective->setPersonal($newData['personal']);

        // Set the start date to the new objective
        $now = new \DateTime("now", new DateTimeZone('Europe/Paris'));
        $newObjective->setStartDate($now);

        // Set the end date to the old objective
        $oldObjective->setEndDate($now);

        // Get the entity manager
        $entityManager = $this->query->getDoctrine()->getManager();

        // Save the objectives
        $entityManager->persist($oldObjective);
        $entityManager->persist($newObjective);

        return $newObjective;
    }
}

