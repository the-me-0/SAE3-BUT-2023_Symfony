<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use App\Domain\Query\HistoryAlertHandler;
use App\Domain\Query\HistoryAlertQuery;
use App\Domain\Query\DateRangeHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\Domain\Query\DateRangeQuery;

use App\Domain\Query\DataHandler;
use App\Domain\Query\DataQuery;
use App\Entity\Room;
use App\Entity\Facility;

use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;

class HistoryProblemsController extends AbstractController
{
    /*
    #[Route('/historique_probleme', name: 'app_history_problems')]
    public function index(Request $request, ManagerRegistry $registry): Response
    {
    
    // Define date start and date end of the query
    $query = new DateRangeQuery(1, 'day');
    $handlerRangeDate = new DateRangeHandler($query);
    $handlerRangeDate->handle();
    // Get all facilities
    $facilities = $registry->getRepository(Facility::class)->findAll();
    $facilites_data = [];
    
    // For each facilities, get all the score of alerts of each rooms and calculate the average
    $facilities_score = array();
    foreach ($facilities as $facility) {
    $rooms = $facility->getRooms();
    $rooms_score = array();
    foreach ($rooms as $room) {
    // Create query and handler
    $query = new HistoryAlertQuery($handlerRangeDate->getStartDate(), $handlerRangeDate->getEndDate(), $room);
    $handlerHistoryAlert = new HistoryAlertHandler(new DataHandler($registry));
    $handlerHistoryAlert->handle($query);
    $rooms_score[] = $handlerHistoryAlert->getScore();
    }
    $facilities_data[] = [
    "facility" => $facility, 
    "score" => (count($rooms_score) > 0) ? round(array_sum($rooms_score) / count($rooms_score),2) : '~',
    ];
    }
    
    return $this->render('history_problems/index.html.twig', [
    'facilities_data' => $facilities_data,
    ]);
    }
    #[Route('/batiment-{facility_id}/historique_probleme/', name: 'app_facility_history_problems')]
    public function facility(int $facility_id, Request $request, ManagerRegistry $registry): Response
    {
    // Define the date range
    $query = new DateRangeQuery(1, 'day');
    $handlerRangeDate = new DateRangeHandler($query);
    $handlerRangeDate->handle();
    
    // Get the facility
    $facility = $registry->getRepository(Facility::class)->find($facility_id);
    
    // Get the score of facility
    $rooms = $facility->getRooms();
    $rooms_score = array();
    foreach ($rooms as $room) {
    // Create query and handler
    $query = new HistoryAlertQuery($handlerRangeDate->getStartDate(), $handlerRangeDate->getEndDate(), $room);
    $handlerHistoryAlert = new HistoryAlertHandler(new DataHandler($registry));
    $handlerHistoryAlert->handle($query);
    $rooms_score[] = $handlerHistoryAlert->getScore();
    }
    
    // Get facility objective
    $objective = $facility->getObjective();
    
    $rooms = $facility->getRooms();
    $rooms_data = [];
    // Get the score of each room
    foreach($rooms as $room) {
    $queryHistoryAlert = new HistoryAlertQuery($handlerRangeDate->getStartDate(), $handlerRangeDate->getEndDate(), $room);
    $handlerHistoryAlert = new HistoryAlertHandler(new DataHandler($registry));
    $handlerHistoryAlert->handle($queryHistoryAlert);
    $rooms_data[] = [
    "room" => $room,
    "score" => $handlerHistoryAlert->getScore(),
    ];
    }
    // Handlers are comming
    
    return $this->render('history_problems/facility.html.twig', [
    'facility' => $facility,
    'rooms_data' => $rooms_data,
    'score' => $score,
    'objective' => $objective,
    ]);
    }
    
    */

    /**
     * @Security("is_granted('ROLE_SV')")
     */
    #[Route('/batiment-{facility_id}/salle-{room_id}/historique_probleme', name: 'app_room_today_history_problems')]
    public function room_today(int $facility_id, int $room_id, Request $request, ManagerRegistry $registry): Response
    {
        /*
         * This function is used to redirect to the room history problem page with the actual date 
        */
        return $this->room($facility_id, $room_id, (new \DateTime("now"))->format('Y-m-d'), $request, $registry);
    }
    
    /**
     * @Security("is_granted('ROLE_SV')")
     */
    #[Route('/batiment-{facility_id}/salle-{room_id}/historique_probleme/{dateString}', name: 'app_room_history_problems')]
    public function room(int $facility_id, int $room_id, string $dateString, Request $request, ManagerRegistry $registry): Response
    {
        // Transform the date : 
        try {
            $date = $this->getDateTime($dateString);
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                "La date n'est pas valide ! Redirigé vers la date du jour."
            );
            return $this->redirectToRoute('app_room_today_history_problems', [
                'facility_id' => $facility_id,
                'room_id' => $room_id,
            ]);
        }


        // Get the actual room
        $room = $registry->getRepository(Room::class)->find($room_id);
        // Get its facility
        if(!$room->getSensor()){
            $this->addFlash(
                'notice',
                "La salle ne possède pas de capteur !"
            );
            return $this->redirectToRoute('app_room', [
                'facility_id' => $facility_id,
                'room_id' => $room_id,

            ]);
        }   
        // Define the date range
        $query = new DateRangeQuery($date);
        $handlerRangeDate = new DateRangeHandler($query);
        $handlerRangeDate->handle();

        // Calcul all incidents
        $handler = new HistoryAlertHandler(new DataHandler($registry));
        $query = new HistoryAlertQuery($handlerRangeDate->getStartDate(), $handlerRangeDate->getEndDate() , $room);
        $handler->handle($query);

        // Get the score of the room
        $score = $handler->getScore();



        // Init variables
        $temperature_alerts = array();
        $co2_alerts = array();
        $humidity_alerts = array();
        $hs_alerts = array();
        $totals = [
            "temp" => "null",
            "co2" => "null",
            "hum" => "null",
        ];

        // Get the temperatures alerts
        $temperature_alerts = $handler->getTempAlert();
        // Get the temperature total
        $totals["temp"] = $handler->getTempHour()->format('%Hh%I');

        // Get the co2 alerts
        $co2_alerts = $handler->getECo2Alert();
        // Get the co2 total
        $totals["co2"] = $handler->getECo2Hour()->format('%Hh%I');

        // Get the humidity alerts
        $humidity_alerts = $handler->getHumidityAlert();
        // Get the humidity total
        $totals["hum"] = $handler->getHumidityHour()->format('%Hh%I');


        return $this->render('history_problems/room.html.twig', [
            'room' => $room,
            'handler' => $handler,
            'score' => $score,
            'facility' => $room->getFacility(),
            'objective' => $room->getObjective(),
            'temperature_alerts' => $temperature_alerts,
            'co2_alerts' => $co2_alerts,
            'humidity_alerts' => $humidity_alerts,
            'totals' => $totals,
        ]);
    }

    private function getDateTime(string $dateString): \DateTime
    {
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        if (!$date) {
            throw new \Exception('Date invalide');
        }
        return $date;
    }

}


