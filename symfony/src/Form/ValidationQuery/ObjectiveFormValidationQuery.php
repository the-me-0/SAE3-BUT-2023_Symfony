<?php

namespace App\Form\ValidationQuery;

use App\Form\Exception\ObjectiveBadTemperatureException;
use App\Form\Exception\ObjectiveBadECO2Exception;
use App\Form\Exception\ObjectiveBadHumidityException;
use App\Form\Exception\ObjectiveBadGapTemperatureException;
use App\Form\Exception\ObjectiveBadGapHumidityException;
use App\Form\Exception\ObjectiveBadGapECO2Exception;

use App\Form\Exception\ValueNotChangeException;

use Symfony\Component\Form\FormInterface;

use phpDocumentor\Reflection\Types\Object_;
use App\Entity\Facility;
use App\Entity\Objective;
use Doctrine\Common\Util\Debug;

class ObjectiveFormValidationQuery
{
    private $form;
    private $objective = null;
    private $element;
    private $doctrine;
    private $applyToAll = false;
    private $type;

    public function __construct($form, $element, $objective, $doctrine, $type)
    {
        /*
         * The query allows to create the form with de good values depending of if it's a room or a facility objective
         */

        $this->type = $type;
        // Check if the form is valid
        if ($form->isValid()) {
            if($type == "facility") {
                // if it's a facility objective, we need to verify if the user wants to apply the objective to all the rooms
                $this->applyToAll = $form->get('applyToAll')->getData();
            }
            

            $changed = false;

            // Get room's facility
            $old_objective = $element->getObjective();

            if ($form->getData()->getTemperature() != $old_objective->getTemperature()) {
                $changed = true;
            }
            if ($form->getData()->getHumidity() != $old_objective->getHumidity()) {
                $changed = true;
            }
            if ($form->getData()->getECO2() != $old_objective->getECO2()) {
                $changed = true;
            }
            if($form->getData()->getGapTemperature() != $old_objective->getGapTemperature()) {
                $changed = true;
            }
            if($form->getData()->getGapHumidity() != $old_objective->getGapHumidity()) {
                $changed = true;
            }
            if($form->getData()->getGapECO2() != $old_objective->getGapECO2()) {
                $changed = true;
            }
            if ($this->applyToAll){
                // If there is at least a room with a personal objectif
                if ($element->getRooms()->exists(
                        function ($key, $room) 
                        {
                            return $room->getObjective()->getPersonal();
                        }
                    )
                ) 
                {
                    Debug::dump("There is a room with a personnal objective");
                    $changed = true;
                }
            }

            if(!$changed) {
                throw new ValueNotChangeException();
            }
            
            $this->objective = $objective;
            $this->form = $form;
            $this->element = $element;
            $this->doctrine = $doctrine;
        } else {
            // Get the errors
            $errors = $form->getErrors(true);
            // Check the errors and throw the corresponding exception
            foreach ($errors as $error) {
                if ($error->getOrigin()->getName() == "temperature") {
                    // Throw the exception passing the error message set in the form constraints
                    throw new ObjectiveBadTemperatureException($error->getMessage());
                }
                if ($error->getOrigin()->getName() == "eCO2") {
                    throw new ObjectiveBadECO2Exception($error->getMessage());
                }
                if ($error->getOrigin()->getName() == "humidity") {
                    throw new ObjectiveBadHumidityException($error->getMessage());
                }
                if ($error->getOrigin()->getName() == "gapTemperature") {
                    throw new ObjectiveBadGapTemperatureException($error->getMessage());
                }
                if ($error->getOrigin()->getName() == "gapECO2") {
                    throw new ObjectiveBadGapECO2Exception($error->getMessage());
                }
                if ($error->getOrigin()->getName() == "gapHumidity") {
                    throw new ObjectiveBadGapHumidityException($error->getMessage());
                }
                else
                {
                    throw new \Exception("Erreur inconnu, veuillez contacter l'administrateur.");
                }
            }
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getObjective()
    {
        return $this->objective;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function getApplyToAll()
    {
        return $this->applyToAll;
    }

    public function getType()
    {
        return $this->type;
    }
}