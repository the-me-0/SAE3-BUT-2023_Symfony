<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Sensor;
use App\Entity\Room;
use App\Entity\Facility;
use App\Form\FacilityType;
use App\Form\RoomType;
use App\Entity\Objective;

// classes for the objective form
use App\Form\ObjectiveFormType;
use App\Form\ValidationHandler\ObjectiveFormValidationHandler;
use App\Form\ValidationQuery\ObjectiveFormValidationQuery;
use App\Form\Exception\ObjectiveBadECO2Exception;
use App\Form\Exception\ObjectiveBadHumidityException;
use App\Form\Exception\ObjectiveBadTemperatureException;
use App\Form\Exception\ObjectiveBadGapECO2Exception;
use App\Form\Exception\ObjectiveBadGapHumidityException;
use App\Form\Exception\ObjectiveBadGapTemperatureException;
use App\Form\Exception\ValueNotChangeException;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use App\Domain\Query\RoomsInAlertHandler;
use App\Domain\Query\RoomsInAlertQuery;
use App\Domain\Alert;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Domain\Datum;
use App\Domain\DataProvider;
use App\Domain\Query\DataHandler;
use App\Domain\Query\DataQuery;
use App\Domain\Query\RoomsListAccessHandler;
use App\Domain\Query\RoomsListAccessQuery;

use Doctrine\Common\Util\Debug;
// Debug::dump($rooms);


class FacilityController extends AbstractController
{

    /* This route allows to display the page of a facility */
    #[Route('/batiment-{facility_id}', name: 'app_facility')]
    public function index(int $facility_id, Request $request, ManagerRegistry $registry): Response
    {

        /* Get the related facility object */
        $facility = $registry->getRepository(Facility::class)->find($facility_id);

        /* Safeguard */
        if (!$facility) {
            // send 404 error
            throw $this->createNotFoundException('Aucun bâtiment trouvé pour l\'ID  ' . $facility_id);
        }

        // get all the rooms of the facility
        $rooms = $registry->getRepository(Room::class)->findBy(['facility' => $facility], ['name' => 'ASC']);
        if($this->getUser()){
            if(!$this->isGranted('ROLE_SEEALL')) {
                $handler = new RoomsListAccessHandler();
                $rooms = $handler->handle(new RoomsListAccessQuery($rooms, $this->getUser()));
            }
        
        }else{
            $rooms = $registry->getRepository(Room::class)->findBy(['private' => false, 'facility' => $facility], ['name' => 'ASC']);
        }

        // get the objective of the facility
        $objective = $facility->getObjective();

        // define needed arrays
        $stageList = array();
        $data = array();

        // Prepare the handler for the rooms in alert
        $rooms_in_alert = array();
        $alertQuery = new RoomsInAlertQuery($rooms);
        $alertHandler = new RoomsInAlertHandler($registry);

        // Prepare the handler for the data
        $dataQuery = new DataQuery(0, '', 5);
        $dataHandler = new DataHandler($registry);


        /* Adds floors and 5 last sent data for each available sensor */
        for ($i = 0; $i < count($rooms); $i++) {

            // if the stage number isn't yet added, add it
            if(!(in_array($rooms[$i]->getFloor(),$stageList)))
            {
                array_push($stageList, $rooms[$i]->getFloor());
            }

            // get the sensor of the room
            $sensor = $this->getDoctrine()->getRepository(Sensor::class)->findByRoom($rooms[$i]->getId());

            // if the sensor exists, get the 5 last data
            if ($sensor != null) {
                $dataQuery->setTag($sensor->getTag());
                $provided = $dataHandler->handle($dataQuery);
                if($provided != []) { $data[$rooms[$i]->getname()] = $provided; }
            }

            if (!isset($data[$rooms[$i]->getname()]))
            {
                continue;
            }
            // check if the room is in alert
            $alertQuery->setRoom(array($rooms[$i]));
            $result = $alertHandler->handle($alertQuery, $data[$rooms[$i]->getname()]);
            if($result != [])
            {
                $rooms_in_alert[] = [
                    'name'=>$rooms[$i]->getname(),
                    'alerts'=>$result
                ];
            }
        }

        // render the html document with all the parameters
        return $this->render('facility/index.html.twig', [
            'controller_name' => 'FacilityController',
            'facility' => $facility,
            'rooms' => $rooms,
            'stages' => $stageList,
            'data'=> $data,
            'objective' => $objective,
            'rooms_in_alert' => $rooms_in_alert,
        ]);
    }

        
    /* This route allows to create a new facility */
    #[Route('/ajouter-batiment', name: 'app_facility_new')]
    public function facility_add(ManagerRegistry $registry, Request $request): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        // Variables Definitions //
        $connexion = $registry->getConnection(); // Define the connection

        // The form creation & modification
        $facility = new Facility(); // create the new sensor
        $form = $this->createForm(FacilityType::class, $facility);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) // check if the form is valid
        {
            /* get the manager needed to interact with the database */
            $em = $registry->getManager();

            /* We save the created facility to the database */
            $em->persist($facility);
            $em->flush();
            
            /* We redirect the user to the home page */
            return $this->redirectToRoute('facility_list');
        }
        
        // The form rendering
        return $this->render('home/new_facility.html.twig', [
            'controller_name' => 'RoomController',
            'form' => $form->createView(),
        ]);
    }

    /* This route allows to delete a facility */
    #[Route('/batiment-{id}/supprimer-batiment', name: 'app_facility_delete')]#[Route('/management/removeFacility/{id}', name: 'app_management_remove_facility')]
    public function facility_remove(int $id, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        /* Find the object related to the id */
        $facility = $registry->getRepository(Facility::class)->findOneBy(['id'=>$id]);

        /* Delete the object */
        $em = $registry->getManager();
        $em->remove($facility);
        $em->flush();

        /* Specifies a redirection (homepage) */
        header("Location: /");

        /* Confirm the deletion */
        return new Response('Votre bâtiment n°'.$id .'à bien été supprimé');
    }

    /* This route allows to edit a facility */
    #[Route('/batiment-{facility_id}/edit', name: 'app_facility_edit')]
    public function facility_edit(int $facility_id, Request $request, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }

        /* Get the repositories we will need */
        $facility = $registry->getRepository(Facility::class)->find($facility_id);
        $rooms = $registry->getRepository(Room::class)->findBy(['facility' => $facility], ['floor' => 'ASC']);

        // Facility form modification
        $facility_form = $this->createForm(FacilityType::class, $facility);
        $facility_form->handleRequest($request);

        /* Form submission handling */
        if($facility_form->isSubmitted() and $facility_form->isValid())
        {
            $em = $registry->getManager();
            if($facility)
            {
                $em->persist($facility);
            }
            $em->flush();
            
            /* Notifies the user of the modification */
            $this->addFlash(
                'notice',
                'Le batiment a bien été modifié !'
            );
            
            /* Redirects the user to the facility page */
            return $this->redirectToRoute('app_facility_edit', [
                'facility_id' => $facility_id,
            ]);
        }

        /* Stage format handling */
        $stageList = array();
        for ($i = 0; $i < count($rooms); $i++) {
            if(!(in_array($rooms[$i]->getFloor(),$stageList)))
            {
                array_push($stageList, $rooms[$i]->getFloor());
            }
        }


        /* Objective form modification */
        $error_form_objective_message = "";

        // Create the objective form
        $form_info = $this->setObjectiveForm($facility, $request);

        // Get the form and the objective
        $objective = $form_info["objective"];
        $form = $form_info["form"];

        // Get the form view
        $objective_form_view = $form->createView();

        // Handle the form submission
        if ($form->isSubmitted())
        {
            // This try catch block is used to handle the exceptions thrown by the form (can't put humidity at 150, etc)
            try {
                // Create the query
                $query = new ObjectiveFormValidationQuery($form, $facility, $objective, $registry, $type="facility");
                $handler = new ObjectiveFormValidationHandler();
                if ($handler->handle($query))
                    $this->addFlash(
                        'notice',
                        'L\'objectif a bien été modifié !'
                    );
                    return $this->redirectToRoute('app_facility_edit', [
                        'facility_id' => $facility_id,
                    ]);
            } catch (ObjectiveBadTemperatureException $e) {
                // Get the error message
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadECO2Exception $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadHumidityException $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadGapHumidityException $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadGapTemperatureException $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadGapECO2Exception $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ValueNotChangeException $e) {
                $error_form_objective_message = $e->getMessage();
            }
        }

        return $this->render('facility/edit.html.twig', [
            'controller_name' => 'FacilityController',
            'facility' => $facility,
            'rooms' => $rooms,
            'stages' => $stageList,
            'objectiveForm' => $objective_form_view,
            'form_objective_error_message' => $error_form_objective_message,
            'facility_form' => $facility_form->createView(),
        ]);
    }


    /* This function allows to create the objective form */
    private function setObjectiveForm($facility, $request)
    {
        // Set the form of objective
        $objective = $facility->getObjective();

        // Create the form cloning the values of the objective
        $form = $this->createForm(ObjectiveFormType::class, clone $objective);

        // Add the checkbox to applay modifications for all theses rooms
        $form->add('applyToAll', CheckboxType::class, [
            'label' => 'Appliquer les objectifs à toutes les pièces',
            'required' => false,
            'mapped' => false
        ]);  

        
        $form->handleRequest($request);
        $form_view = $form->createView();

        /* Return the modified form */
        return array("form" => $form, "objective"=>$objective);
    }

    #[Route('batiment-{facilityId}/download', name: 'download-facility')]
    public function download(ManagerRegistry $registry,$facilityId)
    {
    //if you don't understand this code check symfony/src/Controller/DashboardController function download this function is praticaly the same
        $dataQuery = new DataQuery(0, '' , 10000000 );
        $dataHandler = new DataHandler($registry);
        $facility = $this->getDoctrine()->getRepository(Facility::class)->find($facilityId);
        $rooms = $facility->getRooms();
        $data = array();
                      

            foreach($rooms as $room)
            {
                
                $sensor = $this->getDoctrine()->getRepository(Sensor::class)->findByRoom($room->getId());
    
              
                if ($sensor != null) {
                    $dataQuery->setTag($sensor->getTag());
                    $provided = $dataHandler->handle($dataQuery);
                    if($provided != []) { foreach($provided as $datum) {
                        $data[$room->getName()][] = array(
                            'date' => $datum->getDate(),
                            'name' => $datum->getName(),
                            'desc' => $datum->getDesc(),
                            'value' => $datum->getValue()
                        );
                    } 
                }              
                }
           
                
            }
        
        $fileName = 'data.csv';
        $filePath = '../public/files/'.$fileName;
        $dataString = json_encode($data);
        $file = fopen($filePath, 'w');
        fputcsv($file, array('Salle', 'Date', 'Nom', 'Description', 'Valeur'));
        foreach($data as $room => $roomData) {
            foreach($roomData as $datum) {
                fputcsv($file, array($room, $datum['date'], $datum['name'], $datum['desc'], $datum['value']));
                 }
            }
        fclose($file);
        

        $response = new Response(file_get_contents($filePath));
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        return $response;

        
    }
}