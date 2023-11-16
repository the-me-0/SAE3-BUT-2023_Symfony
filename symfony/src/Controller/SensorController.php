<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use App\Entity\Sensor;
use App\Entity\Room;
use App\Entity\Facility;
use App\Form\SensorType;

use App\Domain\Datum;
use App\Domain\DataProvider;
use App\Domain\Query\DataHandler;
use App\Domain\Query\DataQuery;

class SensorController extends AbstractController
{
    #[Route('/batiment-{facility_id}/salle-{room_id}/capteur-{sensor_id}/edit', name: 'app_sensor')]
    public function index(int $facility_id, int $room_id, int $sensor_id, Request $request, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }

        // Get all the related entities
        $sensor = $registry->getRepository(Sensor::class)->find($sensor_id);
        $room = $sensor->getRoom();
        $facility = $room->getFacility();

        // Get the sensor data
        $data = array();

        $dataQuery = new DataQuery($sensor->getTag(), '', 5);
        $dataHandler = new DataHandler($registry);
        $provided = $dataHandler->handle($dataQuery);
        if($provided != []) { $data = $provided; }

        // The form modification
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        /* The form handling */
        if($form->isSubmitted() and $form->isValid()) // check if the form is valid
        {
            /* Get the entity manager to interact with the database */
            $em = $registry->getManager();

            /* Save the sensor to the database */
            $em->persist($sensor);
            $em->flush();

            /* Notifies the user of this modification */
            $this->addFlash(
                'notice',
                'Le Système d\'acquisition a bien été modifié !'
            );

            /* Redirects to the concerned room */
            return $this->redirectToRoute('app_room_edit', [
                'facility_id' => $facility_id,
                'room_id' => $room_id,
            ]);
        }

        /* Render the edit page */
        return $this->render('sensor/index.html.twig', [
            'data' => $data,
            'form' => $form->createView(),
            'facility' => $facility,
            'room' => $room,
            'sensor' => $sensor,
        ]);
    }

    #[Route('/batiment-{facility_id}/salle-{room_id}/ajouter-sa', name: 'app_as_new')]
    public function sensor_add(int $facility_id, int $room_id, Request $request, ManagerRegistry $registry): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        /* Get the concerned entities */
        $facility = $registry->getRepository(Facility::class)->findOneBy(['id' => $facility_id]);
        $room = $registry->getRepository(Room::class)->findOneBy(['id' => $room_id]);

        /* SafeGuard */
        if(!$room)
        {
            return $this->redirectToRoute('app_home');
        }

        // Check if the room hasn't already a sensor
        $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['room' => $room_id]);
        if($sensor_exist)
        {
            /* Notice the user of the error */
            $this->addFlash(
                'error',
                'La salle a déjà un SA !'
            );
            /* Redirect to the room page */
            return $this->redirectToRoute('app_room_edit', [
                'facility_id' => $facility_id,
                'room_id' => $room_id,
            ]);
        }

        // The form modification
        $sensor = new Sensor(); // create the new sensor
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        /* The form handling */
        if($form->isSubmitted() && $form->isValid()) // check if the form is valid
        {
            // Check if the sensor already exist
            $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['num' => $sensor->getNum()]);
            if (!$sensor_exist) $sensor_exist = $this->getDoctrine()->getRepository(Sensor::class)->findOneBy(['tag' => $sensor->getTag()]);

            /* The sensor hasn't unique num || tag values */
            if($sensor_exist)
            {
                /* Notice the user of the error */
                $this->addFlash(
                    'error',
                    'Le nom ou le tag du SA est déjà utilisé !'
                );
                /* Redirect to the room page */
                return $this->redirectToRoute('app_room', [
                    'facility_id' => $room->getFacility()->getId(),
                    'room_id' => $room->getId(),
                ]);
            }

            // We assume this sensor is a new one and add it to the database
            /* Get the entity manager to interact with the database */
            $em = $registry->getManager();

            /* Save the sensor to the database */
            $sensor->setRoom($room);
            $em->persist($sensor);
            $em->flush();

            /* Notifies the user of this modification */
            $this->addFlash(
                'notice',
                'Le nouveau SA a bien été ajouté !'
            );
            
            /* Redirects to the concerned room */
            return $this->redirectToRoute('app_room', [
                'facility_id' => $room->getFacility()->getId(),
                'room_id' => $room->getId(),
            ]);
        }
        
        /* Render the edit page */
        return $this->render('room/new_sensor.html.twig', [
            'controller_name' => 'RoomController',
            'room' => $room,
            'facility' => $facility,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/batiment-{batimentId}/salle-{roomId}/sensor-{sensorId}/supprimer-sa', name: 'app_remove_sensor')]
    public function updateSensor(int $sensorId, int $batimentId, int $roomId, ManagerRegistry $doctrine): Response
    {
        if(!$this->isGranted('ROLE_EDIT')) {
            throw new AccessDeniedHttpException('You don\'t have the permissions du access this resource.');
        }
        
        /* Get the entity manager to interact with the database */
        $em = $doctrine->getManager();

        /* Get the concerned sensor */
        $sensor = $em->getRepository(Sensor::class)->findOneBy(['id'=>$sensorId]);
        if ($sensor) {
            $em->remove($sensor);
            $em->flush();
        } else {
            $this->addFlash(
                'error',
                'Le capteur n\'existe pas !'
            );
        }
        
        /* Redirects to the concerned room */
        $location = '/batiment-'.$batimentId.'/salle-'.$roomId.'/edit';
        header("Location: ".$location);
        
        return new Response('Vous êtes bien dans la page de suppression des capteurs');
    }

}