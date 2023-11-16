<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ADRESSES //
        $adress_1 = new Adress();
        $adress_1->setNum(7);
        $adress_1->setStreet('Graedel');
        $adress_1->setPostalCode(17000);
        $adress_1->setCity('La Rochelle');
        $adress_1->setCountry('France');
        $manager->persist($adress_1);

        $adress_2 = new Adress();
        $adress_2->setNum(8);
        $adress_2->setStreet('Graedel');
        $adress_2->setPostalCode(17000);
        $adress_2->setCity('La Rochelle');
        $adress_2->setCountry('France');
        $manager->persist($adress_2);

        // FACILITIES //
        $facility_1 = new Facility();
        $facility_1->setName('Informatique-C');
        $facility_1->setSector('IUT');
        $manager->persist($facility_1);

        $facility_2 = new Facility();
        $facility_2->setName('Informatique-D');
        $facility_2->setSector('IUT');
        $manager->persist($facility_2);

        // ROOMS //
        $room_1 = new Room();
        $room_1->setSurface(10);
        $room_1->setNbWindows(3);
        $room_1->setFacility($facility_1);
        $room_1->setNum(206);
        $manager->persist($room_1);

        $room_2 = new Room();
        $room_2->setSurface(11);
        $room_2->setNbWindows(null);
        $room_2->setFacility($facility_1);
        $room_2->setNum(207);
        $manager->persist($room_2);
        
        $room_3 = new Room();
        $room_3->setSurface(11);
        $room_3->setNbWindows(null);
        $room_3->setFacility($facility_2);
        $room_3->setNum(301);
        $manager->persist($room_3);

        $room_4 = new Room();
        $room_4->setSurface(25);
        $room_4->setNbWindows(5);
        $room_4->setFacility($facility_2);
        $room_4->setNum(304);
        $manager->persist($room_4);

        // SENSORS //
        $sensor_1 = new Sensor();
        $sensor_1->setNum('A1547BF');
        $sensor_1->setRoom($room_1);
        /* not here anymore / AND we think about using a SQL check selector */
        // $sensor_1->setType('CO2 | Humidity | Température'); //
        $sensor_1->setDescription('Ceci est un premier sensor test (Et ceci est un easter egg !)');
        $sensor_1->setEnabled(True);
        $manager->persist($sensor_1);

        $sensor_2 = new Sensor();
        $sensor_2->setNum('30SF');
        $sensor_2->setRoom($room_2);
        $sensor_2->setDescription('Ceci est un deuxième sensor test');
        $sensor_2->setEnabled(True);
        $manager->persist($sensor_2);

        $sensor_3 = new Sensor();
        $sensor_3->setNum('F548LKTF');
        $sensor_3->setRoom($room_3);
        $sensor_3->setDescription('Sensor à connecter');
        $sensor_3->setEnabled(False);
        $manager->persist($sensor_3);

        $sensor_4 = new Sensor();
        $sensor_4->setNum('1556458');
        $sensor_4->setRoom($room_4);
        $sensor_4->setDescription('Sensor de la salle 304');
        $sensor_4->setEnabled(True);
        $manager->persist($sensor_4);

        $manager->flush();
    }
}
