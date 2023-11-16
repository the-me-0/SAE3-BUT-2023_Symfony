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

class DataSetFixturesTests extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

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



        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'mot-de-passe'));
        $admin->setLastName('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $tech = new User();
        $tech->setUsername('tech');
        $tech->setPassword($this->passwordEncoder->encodePassword($tech, 'mot-de-passe'));
        $tech->setLastName('tech');
        $tech->setRoles(['ROLE_TECH']);
        $manager->persist($tech);

        $seeAll = new User();
        $seeAll->setUsername('seeAll');
        $seeAll->setPassword($this->passwordEncoder->encodePassword($seeAll, 'mot-de-passe'));
        $seeAll->setLastName('seeAll');
        $seeAll->setRoles(['ROLE_SV']);
        $manager->persist($seeAll);
        
        $visitor = new User();
        $visitor->setUsername('visitor');
        $visitor->setPassword($this->passwordEncoder->encodePassword($visitor, 'mot-de-passe'));
        $visitor->setLastName('visitor');
        $visitor->setRoles(['ROLE_USER']);
        $manager->persist($visitor);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['tests'];
    }
}