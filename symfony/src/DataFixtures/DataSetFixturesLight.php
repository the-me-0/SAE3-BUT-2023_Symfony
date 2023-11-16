<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Facility;
use App\Entity\Room;
use App\Entity\Sensor;
use App\Entity\Objective;

class DataSetFixturesLight extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        $facility_1 = new Facility();
        $facility_1->setName('Informatique-D');
        $facility_1->setSector('IUT');
        $manager->persist($facility_1);

        $objective_1 = new Objective();
        $manager->persist($objective_1);

        $room_1 = new Room();
        $room_1->setSurface(50);
        $room_1->setNbWindows(5);
        $room_1->setFacility($facility_1);
        $room_1->setObjective($objective_1);
        $room_1->setName('D204');
        $room_1->setFloor(2);
        $room_1->setPrivate(false);
        $manager->persist($room_1);


        $sensor_1 = new Sensor();
        $sensor_1->setNum('A1547BF');
        $sensor_1->setRoom($room_1);
        $sensor_1->setDescription('Ceci est un sensor test (et celui qui nous a été attribué)');
        $sensor_1->setEnabled(True);
        $sensor_1->setTag(4);
        $manager->persist($sensor_1);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['light'];
    }
}