<?php

namespace App\Controller;

use App\Domain\Query\RoomsInAlertHandler;
use App\Domain\Query\RoomsInAlertQuery;
use App\Form\Exception\ValueNotChange;
use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


use App\Entity\Sensor;
use App\Entity\Room;
use App\Entity\Facility;
use App\Form\RoomType;
use App\Form\SensorType;
use App\Form\SelectDateFormType;
use App\Form\ObjectiveFormType;
use App\Entity\Objective;
use App\Form\ValidationHandler\ObjectiveFormValidationHandler;
use App\Form\ValidationQuery\ObjectiveFormValidationQuery;
use App\Form\Exception\ObjectiveBadECO2Exception;
use App\Form\Exception\ObjectiveBadHumidityException;
use App\Form\Exception\ObjectiveBadTemperatureException;
use App\Form\Exception\ValueNotChangeException;


use App\Domain\Query\GetRoomObjectiveQuery;
use App\Domain\Query\GetRoomObjectiveHandler;
use App\Domain\Query\DecisionQuery;
use App\Domain\Query\DecisionHandler;

use App\Domain\Alert;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Domain\Datum;
use App\Domain\DataProvider;
use App\Domain\Query\DataHandler;
use App\Domain\Query\DataQuery;

use App\Domain\Module\WeatherModule;

use Symfony\Component\Intl\IntlBundle\IntlBundle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use DateTime;
use DateTimeZone;

use Doctrine\Common\Util\Debug;
// Debug::dump($rooms);



class RoomController extends AbstractController
{
    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/batiment-{facility_id}/salle-{room_id}', name: 'app_room')]
    public function index(int $facility_id, int $room_id, Request $request, ManagerRegistry $registry): Response
    {
        
        // Get the room and the facility from its id
        $room = $registry->getRepository(Room::class)->find($room_id);
        $facility = $registry->getRepository(Facility::class)->find($facility_id);

        $room = $this->getDoctrine()->getRepository(Room::class)->find($room_id);
        $facility = $this->getDoctrine()->getRepository(Facility::class)->find($facility_id);
        
        
        $data = array();
        $dataProvider = new DataProvider();

        // if the room has a sensor
        if($room->getSensor() != null) {
            // get the corresponding data for the sensor
            $query = new DataQuery($room->getSensor()->getTag(), '', 5);
            $dataHandler = new DataHandler($dataProvider, $this->getDoctrine()->getRepository(Sensor::class));
            $provided = $dataHandler->handle($query);
            if($provided != []) { $data[$room->getSensor()->getNum()] = $provided; }
        // ------------- SECURITY CHECK -------------
        if($room->isPrivate()) {
            if(!($room->getOwner()->contains($this->getUser())) and !($this->isGranted('ROLE_SEEALL')))
            {
                $this->addFlash(
                    'notice',
                    "Vous n'êtes pas autorisé à accèder à cette salle"
                );

                return $this->redirectToRoute('app_facility', [
                    'facility_id' => $room->getFacility()->getId(),
                ]);
            }
        }

        /*  ~~~~~~~~~~~~~~~~~~~~~~~~~~~ Get the data of the room ~~~~~~~~~~~~~~~~~~~~~~~~~~~ */   

        $this->getAllData($registry, $room_id);
        
        /* Define the data array that will contain the last data captured */
        $data = array();
        /* This second array is bound to contain the errors possibly declared by the data handler */
        $dataErrors = array();

        // if the room has a sensor
        if($room->getSensor() != null) {
            // get the corresponding data from the sensor
            $dataQuery = new DataQuery($room->getSensor()->getTag(), '', 5);
            $dataHandler = new DataHandler($registry);
            $provided = $dataHandler->handle($dataQuery);
            $data[$room->getSensor()->getNum()] = $provided;

            // get the errors from the data handler
            if($this->isGranted('ROLE_TECH') || $this->isGranted('ROLE_ADMIN')) {
                $dataErrors = $dataHandler->getErrors();
            }
        }

        // Get the objective of the room
        $objective = $room->getObjective();

        // If the objective has personal == false, it means that it is inherited from the facility
        if($room->getObjective()->getPersonal() == false)
            $objective_information = "L'objectif de cette salle est hérité de celui du bâtiment";
        else
            $objective_information = "L'objectif de cette salle lui est propre";


        // Get the alerts of the room
        $query = new RoomsInAlertQuery([$room]);
        $handler = new RoomsInAlertHandler($registry);
        $alerts = $handler->handle($query, $data);

        // Get the weather data
        try {
            $weatherModule = new WeatherModule();
            $dataWeather = $weatherModule->getWeatherData();
        } catch (\Exception $e) {
            $dataWeather = [
                'statusCode' => 500,
                'weatherData' => null,
                'gazData' => null,
                'date' => null
            ];
        }

        $decisions = array();

        // Get the tips of the room if there is data provided
        if($data != [] && $data[$room->getSensor()->getNum()] != [])
        {
            $query = new DecisionQuery($weatherModule, $alerts);
            $handler = new DecisionHandler($query);
            $decisions = $handler->handle();
        }


        $allData = $this->getAllData($registry, $room_id);


        // ------------- LAST 3 DATA -------------
        // Create the data array
        $lastData = ['temp' => [], 'hum' => [], 'co2' => [], 'date' => null];

        if($room->getSensor() != null) {
            /* We go through the provided data and add it to the lastData array */
            foreach ($data[$room->getSensor()->getNum()] as $datum) {
                if ($lastData['temp'] == [] && $datum->getName() == 'temp')
                    $lastData['temp'] = $datum;
                else if ($lastData['hum'] == [] && $datum->getName() == 'hum')
                    $lastData['hum'] = $datum;
                else if ($lastData['co2'] == [] && $datum->getName() == 'co2')
                    $lastData['co2'] = $datum;
            }
        }

        foreach($lastData as $elem) {
            if($elem != []) {
                $lastData['date'] = $elem->getDate();
            }
        }


        // Create Date form
        $dateForm = $this->createForm(SelectDateFormType::class);
        $dateForm->handleRequest($request);
        try {
            if ($dateForm->isSubmitted()) {
                if ($dateForm->isValid()) {
                    $date = $dateForm->getData();
                    $date = $date["date"];

                    // Test if the date is valid
                    if ($date) {
                        return $this->redirectToRoute('app_room_history_problems', [
                            'facility_id' => $facility_id,
                            'room_id' => $room_id,
                            'dateString' => $date->format('Y-m-d')
                        ]);
                    } else {
                        throw new \UnexpectedValueException();
                    }
                } else {
                    throw new \UnexpectedValueException();
                }
            }
        } catch (\UnexpectedValueException $e) {
            $this->addFlash(
                'error',
                "La date entrée n'est pas valide"
            );
        }
        
        /* Returns the room's corresponding page with the loaded parameters */
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
            'sensor' => $room->getSensor(),
            'room' => $room,
            'facility' => $facility,
            'data' => $data,
            'allData' => $allData,
            'objective' => $objective,
            'objective_information' => $objective_information,
            'alerts' => $alerts,
            'tips' => $decisions,
            'statusCode' => $dataWeather['statusCode'],
            'weatherData' => $dataWeather['weatherData'],
            'date' => $dataWeather['date'],
            'gaz' => $dataWeather['gazData'],
            'dataErrors' => $dataErrors,
            'lastData' => $lastData,
            'form_date' => $dateForm->createView(),
        ]);
    }

    #[Route('/batiment-{facility_id}/ajouter-salle', name: 'app_room_new')]
    public function room_add(int $facility_id, Request $request, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        // Get the facility entity
        $facility = $registry->getRepository(Facility::class)->find($facility_id);
        
        // The form modification
        $room = new Room(); // create the new sensor
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        /* The form handling */
        if($form->isSubmitted() and $form->isValid()) // check if the form is valid
        {
            /* Get the entityManager to interact with the database */
            $em = $registry->getManager();

            /* Set the room's facility */
            $room->setFacility($facility);

            /* Create the objective */
            $objective = new Objective();
            $now = new \DateTime("now", new DateTimeZone('Europe/Paris'));
            $now->setTime(date('H'), date('i'), date('s'));
            $objective->setStartDate($now);
            $room->setObjective($objective);

            /* Save the room in the database */
            $em->persist($room);
            $em->flush();
            
            /* Redirect to the concerned facility */
            return $this->redirectToRoute('app_facility', [
                'facility_id' => $facility_id,
            ]);
        }

        /* The form rendering */
        return $this->render('facility/new_room.html.twig', [
            'controller_name' => 'RoomController',
            'facility' => $facility_id,
            'form' => $form->createView(),
            'facility_name' => $facility->getName(),
        ]);
    }

    #[Route('/batiment-{batimentId}/salle-{roomId}/supprimer-salle', name: 'app_delete_room')]
    public function room_remove(int $roomId, int $batimentId, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        /* Get the room repository */
        $room = $registry->getRepository(Room::class)->findOneBy(['id'=>$roomId]);
        
        /* Get the entity manager to interact with the database */
        $em = $registry->getManager();
        
        $em->remove($room);
        $em->flush();

        /* Redirection */
        $location = '/batiment-'.$batimentId;
        header("Location: ".$location);

        return new Response('Vous êtes bien dans la page de suppression des salles');
    }

    #[Route('/batiment-{facility_id}/salle-{room_id}/edit', name: 'app_room_edit')]
    public function room_edit(int $facility_id, int $room_id, Request $request, ManagerRegistry $registry): Response
    {

        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }

        // get needed entities
        $facility = $registry->getRepository(Facility::class)->findOneBy(['id' => $facility_id]);
        $room = $registry->getRepository(Room::class)->findOneBy(['id' => $room_id]);

        // The form modification
        $room_form = $this->createForm(RoomType::class, $room);
        $room_form->handleRequest($request);

        /* The ROOM form handling */
        if($room_form->isSubmitted() && $room_form->isValid())
        {
            /* Get the entity manager to interact with the database */
            $em = $registry->getManager();

            $em->persist($room);
            $em->flush();
            
            /* Notice the user of the modification */
            $this->addFlash(
                'notice',
                'La salle a bien été modifié !'
            );

            /* Redirect to the room */
            return $this->redirectToRoute('app_room', [
                'facility_id' => $room->getFacility()->getId(),
                'room_id' => $room->getId(),
            ]);
        }

        /* The OBJECTIVE form modification */
        $error_form_objective_message = "";

        // Create the objective form
        $form_info = $this->setObjectiveForm($room, $registry, $request);

        // Get the form and the objective
        $objective = $form_info["objective"];
        $form = $form_info["form"];

        // Get the form view
        $objective_form_view = $form->createView();

        // If the objective is from facility, indicate it in objective_information
        if($room->getObjective()->getPersonal() == false)
            $objective_information = "L'objectif de cette salle est hérité de celui du bâtiment";
        else
            $objective_information = "L'objectif de cette salle lui est propre";

        
        /* The OBJECTIVE form handling */
        if ($form->isSubmitted())
        {
            try {
                // Create the query
                $query = new ObjectiveFormValidationQuery($form, $room, $objective, $registry, $type="room");
                $handler = new ObjectiveFormValidationHandler();
                if ($handler->handle($query))
                {
                    $this->addFlash(
                        'notice',
                        'L\'objectif salle a bien été modifié !'
                    );
                    
                    return $this->redirectToRoute('app_room_edit', [
                        'facility_id' => $room->getFacility()->getId(),
                        'room_id' => $room->getId(),
                    ]);
                }
            } catch (ObjectiveBadTemperatureException $e) {
                // Get the error message
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadECO2Exception $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ObjectiveBadHumidityException $e) {
                $error_form_objective_message = $e->getMessage();
            } catch (ValueNotChangeException $e) {
                $error_form_objective_message = $e->getMessage();
            }
        }

<<<<<<< HEAD

=======
        /* Render the EDIT page */
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd
        return $this->render('room/edit.html.twig', [
            'controller_name' => 'RoomController',
            'room' => $room,
            'facility' => $facility,
            'room_form' => $room_form->createView(),
            'objectiveForm' => $objective_form_view,
            'form_objective_error_message' => $error_form_objective_message,
            'sensor' => $room->getSensor(),
            'objective_information' => $objective_information,
        ]);
    }

<<<<<<< HEAD
    #[Route('/batiment-{facility_id}/salle-{room_id}/ajouter-sa', name: 'app_as_new')]
    public function sensor_add(int $facility_id, int $room_id, Request $request): Response
    {
        $facility = $this->getDoctrine()->getRepository(Facility::class)->findOneBy(['id' => $facility_id]);
        $room = $this->getDoctrine()->getRepository(Room::class)->findOneBy(['id' => $room_id]); // the room we are editing
        if(!$room)
        {
            return $this->redirectToRoute('app_home');
        }

        // Check if the room hasn't already a sensor
        $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['room' => $room_id]);
        if($sensor_exist)
        {
            $this->addFlash(
                'error',
                'La salle a déjà un SA !'
            );
            return $this->redirectToRoute('app_room_edit', [
                'facility_id' => $facility_id,
                'room_id' => $room_id,
            ]);
        }

        // The form modification
        $sensor = new Sensor(); // create the new sensor
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) // check if the form is valid
        {
            // Check if the sensor already exist
            $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['num' => $sensor->getNum()]);
            if (!$sensor_exist) $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['tag' => $sensor->getTag()]);
            if($sensor_exist)
            {
                $this->addFlash(
                    'error',
                    'Le nom ou le tag du SA est déjà utilisé !'
                );
                return $this->redirectToRoute('app_room', [
                    'facility_id' => $room->getFacility()->getId(),
                    'room_id' => $room->getId(),
                ]);
            }

            // We assume this sensor is a new one and add it to the database
            $entityManager = $this->getDoctrine()->getManager();
            $sensor->setRoom($room);
            $entityManager->persist($sensor);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Le nouveau SA a bien été ajouté !'
            );
            
            return $this->redirectToRoute('app_room', [
                'facility_id' => $room->getFacility()->getId(),
                'room_id' => $room->getId(),
            ]);
        }
        
        return $this->render('room/new_sensor.html.twig', [
            'controller_name' => 'RoomController',
            'room' => $room,
            'facility' => $facility,
            'form' => $form->createView(),
            'facility_id' => $facility_id,
            'room_id' => $room_id,
        ]);
    }

=======
    /* Modifies the objective form */
>>>>>>> 8df0513a3810338a9dc3a94aa0caac74c6ba07fd
    private function setObjectiveForm($room, $doctrine, $request)
    {
        // Set the form of objective

        // Get the objective of the room with its handler
        $objective = $room->getObjective();
        
        // Create the form cloning the objective
        $form = $this->createForm(ObjectiveFormType::class, clone $objective);

        $form->handleRequest($request);
        $form_view = $form->createView();


        return array("form" => $form, "objective"=>$objective);
    }

    private function objectiveFormSubmitted($form, $objective, $room, $doctrine)
    {
        // Set the result of the form of objective if submitted
        $entityManager = $doctrine->getManager();
        $entityManager->persist($objective);
        $room->setObjective($objective);
        $entityManager->persist($room);
        $entityManager->flush();
        return $this->redirectToRoute('app_room', [
            'room_id' => $room->getId(),
        ]);
    }

    #[Route('/room/data/{roomId}', name: 'app_room_data')]
    public function getAllData(ManagerRegistry $registry, int $roomId): Response
    {
        $room = $registry->getRepository(Room::class)->find($roomId);

        $allData = array();

        // get all data from all sensors
        if($room->getSensor() != null) {
            // get the corresponding data from the sensor
            $dataQuery = new DataQuery($room->getSensor()->getTag(), '');
            $dataHandler = new DataHandler($registry);
            $provided = $dataHandler->handle($dataQuery);
            if($provided != [])
            {
                foreach ($provided as $datum ) {
                    if ($datum != [])
                    {
                        $correctedDate = DateTime::createFromFormat('d-m-Y H:i:s', $datum->getDate());

                        $allData[] = [
                            'date' => $correctedDate->format('Y-m-d H:i:s'),
                            $datum->getName() => $datum->getValue()
                        ];
                    }
                }
            }
        }

        return $this->json([
            "allData" => $allData
        ]);
    }


    #[Route('/batiment-{batimentId}/salle-{roomId}/download', name: 'download-rooms')]
     
    public function download(ManagerRegistry $registry,$roomId)
    {
        //if you don't understand this code check symfony/src/Controller/DashboardController function download this function is praticaly the same
        

        $dataQuery = new DataQuery(0, '' , 10000000 );
        $dataHandler = new DataHandler($registry);
        $room = $registry->getRepository(Room::class)->find($roomId);
        $data = array();
            
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

