<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\MyEntityRepository;
use App\Entity\Room;
use App\Entity\Sensor;
use App\Domain\Query\DataQuery;
use App\Domain\Query\DataHandler;
use App\Domain\DataProvider;
use App\Domain\Query\RoomsInAlertHandler;
use App\Domain\Query\RoomsInAlertQuery;
use Doctrine\Persistence\ManagerRegistry;
use PHPExcel;
use PHPExcel_IOFactory;



use Doctrine\Common\Util\Debug;


class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // in the JS file, we will have a function that will be called every 60 seconds and will update the dashboard

        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/dashboard/reload', name: 'app_dashboard_reload')]
    public function reload(ManagerRegistry $registry): Response
    {
        /* Get the needed data */
        $rooms_in_alert = $this->roomsInAlert($registry);
        $acquisition_systems_not_connected = $this->acquisitionSystemNotConnected($registry);
        $rooms_without_acquisition_system = $this->roomsWithoutAcquisitionSystem($registry);

        return $this->json([
            "rooms_in_alert" => $rooms_in_alert->getContent(),
            "acquisition_systems_not_connected" => $acquisition_systems_not_connected->getContent(),
            "rooms_without_acquisition_system" => $rooms_without_acquisition_system->getContent(),
        ]);
    }

    #[Route('/dashboard/reload/roomsInAlert', name: 'app_dashboard_reload_roomsInAlert')]
    public function roomsInAlert(ManagerRegistry $registry): Response
    {
        $rooms = $registry->getRepository(Room::class)->findAll();
        $rooms_in_alert = array();

        if (!$rooms) {
            return $rooms_in_alert;
        }

        // Prepare the handler for the data
        $dataQuery = new DataQuery(0, '', 5);
        $dataHandler = new DataHandler($registry);

        // Prepare the handler for the rooms in alert
        $alertQuery = new RoomsInAlertQuery($rooms);
        $alertHandler = new RoomsInAlertHandler($registry);

        /* Adds floors and 5 last sent data for each available sensor */
        for ($i = 0; $i < count($rooms); $i++) {

            $provided = array();

            // get the sensor of the room
            $sensor = $this->getDoctrine()->getRepository(Sensor::class)->findByRoom($rooms[$i]->getId());

            // if the sensor exists, get the 5 last data
            if ($sensor != null) {
                $dataQuery->setTag($sensor->getTag());
                $provided = $dataHandler->handle($dataQuery);
            }

            if ($provided == [])
            {
                continue;
            }

            // check if the room is in alert
            $alertQuery->setRoom(array($rooms[$i]));
            $result = $alertHandler->handle($alertQuery, $provided);
            if($result != [])
            {
                $rooms_in_alert[] = [
                    'room'=>$rooms[$i],
                    'alerts'=>$result
                ];
            }
        }

        return $this->render('dashboard/roomsInAlert.html.twig', [
            'rooms_in_alert' => $rooms_in_alert,
        ]);
    }

    // #[Route('/dashboard/reload/acquisitionSystemNotConnected', name: 'app_dashboard_reload_acquisitionSystemNotConnected')]
    public function acquisitionSystemNotConnected(ManagerRegistry $registry): Response
    {
        $acquisition_systems_not_connected = $registry->getRepository(Sensor::class)->findSensorsNotConnected();

        return $this->render('dashboard/acquisitionSystemNotConnected.html.twig', [
            'acquisition_systems_not_connected' => $acquisition_systems_not_connected,
        ]);
    }

    // #[Route('/dashboard/reload/roomsWithoutAcquisitionSystem', name: 'app_dashboard_reload_roomsWithoutAcquisitionSystem')]
    public function roomsWithoutAcquisitionSystem(ManagerRegistry $registry): Response
    {
        $rooms_without_acquisition_system = $registry->getRepository(Room::class)->findRoomsWithoutAcquisitionSystem();

        return $this->render('dashboard/roomsWithoutAcquisitionSystem.html.twig', [
            'rooms_without_acquisition_system' => $rooms_without_acquisition_system,
        ]);
    }

    
    #[Route('dashboard/download', name: 'download-all')]
     
    public function download(ManagerRegistry $registry)
    {
        $dataQuery = new DataQuery(0, '' , 10000000 ); 
        $dataHandler = new DataHandler($registry);
        $rooms = $registry->getRepository(Room::class)->FindAll(); //search all rooms in the databases
        $data = array();


        foreach($rooms as $room)
        {
            
            $sensor = $this->getDoctrine()->getRepository(Sensor::class)->findByRoom($room->getId());// search the sensor of room

          
            if ($sensor != null) { // check sensor not nul
                $dataQuery->setTag($sensor->getTag());// set tag 
                $provided = $dataHandler->handle($dataQuery); // recover all data
                if($provided != []) { foreach($provided as $datum) { // puts all the data in the table
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
        
        $fileName = 'data.csv'; // name of the file who the data are printed
        $filePath = '../public/files/'.$fileName; // path where the file is saved
        $dataString = json_encode($data); // convert data to json 
        $file = fopen($filePath, 'w');// open file data.csv
        fputcsv($file, array('Salle', 'Date', 'Nom', 'Description', 'Valeur')); // print the attributes in Data.csv
        foreach($data as $room => $roomData) {
            foreach($roomData as $datum) {
                fputcsv($file, array($room, $datum['date'], $datum['name'], $datum['desc'], $datum['value'])); // print all data
                 }
            }
        fclose($file);//close file 
        

        $response = new Response(file_get_contents($filePath));//send a download resquet to the web site
        $response->headers->set('Content-Type', 'application/force-download'); // type of content
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"'); // attachement send
        return $response;

        
    }

}