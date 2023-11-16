<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Domain\Query\RoomsInAlertQuery;
use App\Domain\Query\RoomsInAlertHandler;
use App\Entity\Room;
use App\Domain\Datum;
use App\Domain\Alert;

class RoomsInAlertTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function testRoomsInAlertQuery(): void
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $room = $entityManager->getRepository(Room::class)->findOneBy(['name' => 'D204']);
        
        $this->assertInstanceOf(Room::class, $room);

        $query = new RoomsInAlertQuery([$room]);

        $this->assertEquals([$room], $query->getRooms());
    }

    public function testRoomsInAlertHandler(): void
    {
        // $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        // $registry = self::$container->get('doctrine.orm.manager_registry');
        $registry = $this->getContainer()->get('doctrine');
        $room = $entityManager->getRepository(Room::class)->findOneBy(['name' => 'D204']);
        $query = new RoomsInAlertQuery([$room]);

        $handler = new RoomsInAlertHandler($registry);

        /* As we pass a datum array to the handler, we can test it by providing test data */
        $datumArray = array();
        $datumArray[] = new Datum(
            $room,
            '18-01-2023 12:00:00',
            'temp',
            'Temperature of the sensor #_X2eq1',
            25.9
        );
        $datumArray[] = new Datum(
            $room,
            '18-01-2023 12:00:00',
            'co2',
            'eCo2 of the sensor #_X2eq1',
            689.2
        );
        $datumArray[] = new Datum(
            $room,
            '18-01-2023 12:00:00',
            'hum',
            'Humidity of the sensor #_X2eq1',
            10
        );

        /* Modify the room's objective */
        $objective = $room->getObjective();
        $objective->setTemperature(20);
        $objective->setHumidity(30);
        $objective->setECO2(500);
        $objective->setGapTemperature(0);
        $objective->setGapHumidity(0);
        $objective->setGapECO2(0);
        /* Save the objective */
        $entityManager->persist($objective);
        $entityManager->flush();

        $roomsInAlert = $handler->handle($query, $datumArray);
        
        /* Assert that roomsInAlert contains 3 alerts */
        $this->assertEquals(3, count($roomsInAlert));

        /* Assert that roomsInAlert contains Alert */
        $this->assertInstanceOf(Alert::class, $roomsInAlert[0]);

        /* Assert that roomsInAlert contains the right alerts */
        $this->assertEquals('temp', $roomsInAlert[0]->getType());
        $this->assertEquals('hum', $roomsInAlert[1]->getType());
        $this->assertEquals('co2', $roomsInAlert[2]->getType());

        /* Assert that roomsInAlert contains the right alerts */
        $this->assertEquals('higher', $roomsInAlert[0]->getPosition());
        $this->assertEquals('lower', $roomsInAlert[1]->getPosition());
        $this->assertEquals('higher', $roomsInAlert[2]->getPosition());

        /* Assert that roomsInAlert contains the right alerts */
        $this->assertEquals("La température est trop haute de 5.9°C", $roomsInAlert[0]->getAlertMessage());
        $this->assertEquals("L'humidité est trop basse de -20%", $roomsInAlert[1]->getAlertMessage());
        $this->assertEquals("Le ECO2 est trop élevé de 189.2ppm", $roomsInAlert[2]->getAlertMessage());
    }
}
