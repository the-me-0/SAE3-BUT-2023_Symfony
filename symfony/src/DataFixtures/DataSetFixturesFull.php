<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Facility;
use App\Entity\Room;
use App\Entity\Sensor;
use App\Entity\Objective;
use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
        
class DataSetFixturesFull extends Fixture implements FixtureGroupInterface
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

        $facility_1 = new Facility();
        $facility_1->setName('Informatique - D');
        $facility_1->setSector('IUT');
        $manager->persist($facility_1);

        $facility_2 = new Facility();
        $facility_2->setName('Réseaux et Télécoms - C');
        $facility_2->setSector('IUT');
        $manager->persist($facility_2);


        // Salle D205 --> Tag 1
        $room_205 = new Room();
        $room_205->setSurface(50);
        $room_205->setNbWindows(4);
        $room_205->setFacility($facility_1);
        $room_205->setName('D205');
        $room_205->setFloor(2);
        $room_205->setPrivate(false);
        $manager->persist($room_205);

        $sensor_1 = new Sensor();
        $sensor_1->setNum('#_x1eq1');
        $sensor_1->setRoom($room_205);
        $sensor_1->setDescription('Système d\'acquisition attribué aux X1eq1 salle D205');
        $sensor_1->setTag(1);
        $manager->persist($sensor_1);


        // Salle D206 --> Tag 2
        $room_206 = new Room();
        $room_206->setSurface(50);
        $room_206->setNbWindows(4);
        $room_206->setFacility($facility_1);
        $room_206->setName('D206');
        $room_206->setFloor(2);
        $room_206->setPrivate(false);
        $manager->persist($room_206);

        $sensor_2 = new Sensor();
        $sensor_2->setNum('#_x1eq2');
        $sensor_2->setRoom($room_206);
        $sensor_2->setDescription('Système d\'acquisition attribué aux X1eq2 salle D206');
        $sensor_2->setTag(2);
        $manager->persist($sensor_2);


        // Salle D207 --> Tag 3
        $room_207 = new Room();
        $room_207->setSurface(50);
        $room_207->setNbWindows(4);
        $room_207->setFacility($facility_1);
        $room_207->setName('D207');
        $room_207->setFloor(2);
        $room_207->setPrivate(false);
        $manager->persist($room_207);

        $sensor_3 = new Sensor();
        $sensor_3->setNum('#_x1eq3');
        $sensor_3->setRoom($room_207);
        $sensor_3->setDescription('Système d\'acquisition attribué aux X1eq3 salle D207');
        $sensor_3->setTag(3);
        $manager->persist($sensor_3);


        // Salle D204 --> Tag 4
        $room_204 = new Room();
        $room_204->setSurface(50);
        $room_204->setNbWindows(5);
        $room_204->setFacility($facility_1);
        $room_204->setName('D204');
        $room_204->setFloor(2);
        $room_204->setPrivate(false);
        $manager->persist($room_204);

        $sensor_4 = new Sensor();
        $sensor_4->setNum('#_x2eq1');
        $sensor_4->setRoom($room_204);
        $sensor_4->setDescription('Système d\'acquisition attribué aux X2eq1 salle D204');
        $sensor_4->setTag(4);
        $manager->persist($sensor_4);


        // Salle D203 --> Tag 5
        $room_203 = new Room();
        $room_203->setSurface(50);
        $room_203->setNbWindows(4);
        $room_203->setFacility($facility_1);
        $room_203->setName('D203');
        $room_203->setFloor(2);
        $room_203->setPrivate(false);
        $manager->persist($room_203);

        $sensor_5 = new Sensor();
        $sensor_5->setNum('#_x2eq2');
        $sensor_5->setRoom($room_203);
        $sensor_5->setDescription('Système d\'acquisition attribué aux X2eq2 salle D203');
        $sensor_5->setTag(5);
        $manager->persist($sensor_5);


        // Salle D303 --> Tag 6
        $room_303 = new Room();
        $room_303->setSurface(50);
        $room_303->setNbWindows(4);
        $room_303->setFacility($facility_1);
        $room_303->setName('D303');
        $room_303->setFloor(3);
        $room_303->setPrivate(false);
        $manager->persist($room_303);

        $sensor_6 = new Sensor();
        $sensor_6->setNum('#_x2eq3');
        $sensor_6->setRoom($room_303);
        $sensor_6->setDescription('Système d\'acquisition attribué aux X2eq3 salle D303');
        $sensor_6->setTag(6);
        $manager->persist($sensor_6);

        // Salle D304 --> Tag 7
        $room_304 = new Room();
        $room_304->setSurface(50);
        $room_304->setNbWindows(4);
        $room_304->setFacility($facility_1);
        $room_304->setName('D304');
        $room_304->setFloor(3);
        $room_304->setPrivate(false);
        $manager->persist($room_304);

        $sensor_7 = new Sensor();
        $sensor_7->setNum('#_y1eq1');
        $sensor_7->setRoom($room_304);
        $sensor_7->setDescription('Système d\'acquisition attribué aux Y1eq1 salle D304');
        $sensor_7->setEnabled(True);
        $sensor_7->setTag(7);
        $manager->persist($sensor_7);


        // Salle C101 --> Tag 8
        $room_101 = new Room();
        $room_101->setSurface(50);
        $room_101->setNbWindows(4);
        $room_101->setFacility($facility_2);
        $room_101->setName('C101');
        $room_101->setFloor(1);
        $room_101->setPrivate(true);
        $manager->persist($room_101);

        $sensor_8 = new Sensor();
        $sensor_8->setNum('#_y1eq2');
        $sensor_8->setRoom($room_101);
        $sensor_8->setDescription('Système d\'acquisition attribué aux Y1eq2 salle C101');
        $sensor_8->setTag(8);
        $manager->persist($sensor_8);


        // Salle D109 --> Tag 9
        $room_109 = new Room();
        $room_109->setSurface(50);
        $room_109->setNbWindows(4);
        $room_109->setFacility($facility_1);
        $room_109->setName('D109');
        $room_109->setFloor(1);
        $room_109->setPrivate(true);
        $manager->persist($room_109);

        $sensor_9 = new Sensor();
        $sensor_9->setNum('#_y1eq3');
        $sensor_9->setRoom($room_109);
        $sensor_9->setDescription('Système d\'acquisition attribué aux Y1eq3 salle D109');
        $sensor_9->setTag(9);
        $manager->persist($sensor_9);


        // Salle Secreteriat --> Tag 10
        $Secreteriat = new Room();
        $Secreteriat->setSurface(50);
        $Secreteriat->setNbWindows(4);
        $Secreteriat->setFacility($facility_1);
        $Secreteriat->setName('Secreteriat');
        $Secreteriat->setFloor(1);
        $Secreteriat->setPrivate(true);
        $manager->persist($Secreteriat);

        $sensor_10 = new Sensor();
        $sensor_10->setNum('#_y2eq1');
        $sensor_10->setRoom($Secreteriat);
        $sensor_10->setDescription('Système d\'acquisition attribué aux Y2eq1 salle Secretariat');
        $sensor_10->setTag(10);
        $manager->persist($sensor_10);

        // Salle D001 --> Tag 11
        $room_001 = new Room();
        $room_001->setSurface(50);
        $room_001->setNbWindows(4);
        $room_001->setFacility($facility_1);
        $room_001->setName('D001');
        $room_001->setFloor(0);
        $room_001->setPrivate(false);
        $manager->persist($room_001);

        $sensor_11 = new Sensor();
        $sensor_11->setNum('#_y2eq2');
        $sensor_11->setRoom($room_001);
        $sensor_11->setDescription('Système d\'acquisition attribué aux Y2eq2 salle D001');
        $sensor_11->setTag(11);
        $manager->persist($sensor_11);


        // Salle D002 --> Tag 12
        $room_002 = new Room();
        $room_002->setSurface(50);
        $room_002->setNbWindows(4);
        $room_002->setFacility($facility_1);
        $room_002->setName('D002');
        $room_002->setFloor(0);
        $room_002->setPrivate(false);
        $manager->persist($room_002);

        $sensor_12 = new Sensor();
        $sensor_12->setNum('#_y2eq3');
        $sensor_12->setRoom($room_002);
        $sensor_12->setDescription('Système d\'acquisition attribué aux Y2eq3 salle D002');
        $sensor_12->setTag(12);
        $manager->persist($sensor_12);


        // Salle D004 --> Tag 13
        $room_004 = new Room();
        $room_004->setSurface(50);
        $room_004->setNbWindows(4);
        $room_004->setFacility($facility_1);
        $room_004->setName('D004');
        $room_004->setFloor(0);
        $room_004->setPrivate(false);
        $manager->persist($room_004);

        $sensor_13 = new Sensor();
        $sensor_13->setNum('#_z1eq1');
        $sensor_13->setRoom($room_004);
        $sensor_13->setDescription('Système d\'acquisition attribué aux Z1eq1 salle D004');
        $sensor_13->setTag(13);
        $manager->persist($sensor_13);


        // Salle C003 --> Tag 14
        $room_003 = new Room();
        $room_003->setSurface(50);
        $room_003->setNbWindows(4);
        $room_003->setFacility($facility_2);
        $room_003->setName('C003');
        $room_003->setFloor(0);
        $room_003->setPrivate(false);
        $manager->persist($room_003);

        $sensor_14 = new Sensor();
        $sensor_14->setNum('#_z1eq2');
        $sensor_14->setRoom($room_003);
        $sensor_14->setDescription('Système d\'acquisition attribué aux Z1eq2 salle C003');
        $sensor_14->setTag(14);
        $manager->persist($sensor_14);


        // Salle C007 --> Tag 15
        $room_007 = new Room();
        $room_007->setSurface(50);
        $room_007->setNbWindows(4);
        $room_007->setFacility($facility_2);
        $room_007->setName('C007');
        $room_007->setFloor(0);
        $room_007->setPrivate(false);
        $manager->persist($room_007);

        $sensor_15 = new Sensor();
        $sensor_15->setNum('#_z1eq3');
        $sensor_15->setRoom($room_007);
        $sensor_15->setDescription('Système d\'acquisition attribué aux Z1eq3 salle C007');
        $sensor_15->setTag(15);
        $manager->persist($sensor_15);


        /* Users creation */
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'password'));
        $admin->setLastName('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $tech = new User();
        $tech->setUsername('technician');
        $tech->setPassword($this->passwordEncoder->encodePassword($tech, 'password'));
        $tech->setLastName('tech');
        $tech->setRoles(['ROLE_TECH']);
        $manager->persist($tech);

        $seeAll = new User();
        $seeAll->setUsername('supervisor');
        $seeAll->setPassword($this->passwordEncoder->encodePassword($seeAll, 'password'));
        $seeAll->setLastName('seeAll');
        $seeAll->setRoles(['ROLE_SV']);
        $manager->persist($seeAll);
        
        $visitor = new User();
        $visitor->setUsername('visitor');
        $visitor->setPassword($this->passwordEncoder->encodePassword($visitor, 'password'));
        $visitor->setLastName('visitor');
        $visitor->setRoles(['ROLE_USER']);
        $manager->persist($visitor);


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['full'];
    }
}
