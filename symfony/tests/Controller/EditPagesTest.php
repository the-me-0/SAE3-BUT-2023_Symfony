<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Entity\User;
use App\Entity\Facility;
use App\Entity\Room;
use App\Entity\Sensor;

class EditPagesTest extends WebTestCase
{
    public function testEditFacility(): void
    {
        $client = static::createClient();

        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        /* Get the reositories */
        $userRepository = $entityManager->getRepository(User::class);
        $facilityRepository = $entityManager->getRepository(Facility::class);

        /* Get the entities we need */
        $user = $userRepository->findOneBy(['username' => 'admin']);
        $facility = $facilityRepository->findOneBy(['name' => 'Informatique-D']);

        /* We need to be connected to access the edit pages */
        $client->loginUser($user);

        /* Make the request */
        $crawler = $client->request('GET', '/batiment-'.$facility->getId().'/edit');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        /* Get the form */
        $submitFacility = $crawler->filter('form[name=facility]')->form();

        /* Fill the form */
        $submitFacility['facility[sector]'] = 'Université';
        
        /* Submit the form */
        $client->submit($submitFacility);

        /* Check the response */
        $this->assertSame(302, $client->getResponse()->getStatusCode());

        /* Check the database */
        $facility = $facilityRepository->findOneBy(['name' => 'Informatique-D']);
        $this->assertSame('Université', $facility->getSector());

        /* Check the redirection to the add page */
        $link = $crawler->filter('a.add_button')->link();
        $crawler = $client->click($link);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testEditRoom(): void
    {
        $client = static::createClient();

        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        /* Get the repositories */
        $userRepository = $entityManager->getRepository(User::class);
        $roomRepository = $entityManager->getRepository(Room::class);

        /* Get the entities we need */
        $user = $userRepository->findOneBy(['username' => 'admin']);
        $room = $roomRepository->findOneBy(['name' => 'D204']);

        /* We need to be connected to access the edit pages */
        $client->loginUser($user);

        /* Make the request */
        $crawler = $client->request('GET', '/batiment-'.$room->getFacility()->getId().'/salle-'.$room->getId().'/edit');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        /* Get the form */
        $formRoom = $crawler->filter('form[name=objective_form]')->form();

        /* Fill the form */
        $formRoom['objective_form[gapHumidity]'] = 20;
        
        /* Submit the form */
        $client->submit($formRoom);

        /* Check the response */
        $this->assertSame(302, $client->getResponse()->getStatusCode());

        /* Check the database */
        $room = $roomRepository->findOneBy(['name' => 'D204']);
        $this->assertSame(20, $room->getObjective()->getGapHumidity());

        /* Check the redirection to the add page */
        $link = $crawler->filter('a.return_button')->link();
        $crawler = $client->click($link);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testEditSensor(): void
    {
        $client = static::createClient();

        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        /* Get the repositories */
        $userRepository = $entityManager->getRepository(User::class);
        $sensorRepository = $entityManager->getRepository(Sensor::class);

        /* Get the entities we need */
        $user = $userRepository->findOneBy(['username' => 'admin']);
        $sensor = $sensorRepository->findOneBy(['num' => 'A1547BF']);

        /* We need to be connected to access the edit pages */
        $client->loginUser($user);

        /* Make the request */
        $crawler = $client->request('GET', '/batiment-'.$sensor->getRoom()->getFacility()->getId().'/salle-'.$sensor->getRoom()->getId().'/capteur-'.$sensor->getId().'/edit');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        /* Get the form */
        $formRoom = $crawler->filter('form[name=sensor]')->form();

        /* Fill the form */
        $formRoom['sensor[description]'] = "test description";
        
        /* Submit the form */
        $client->submit($formRoom);

        /* Check the response */
        $this->assertSame(302, $client->getResponse()->getStatusCode());

        /* Check the database */
        $sensor = $sensorRepository->findOneBy(['num' => 'A1547BF']);
        $this->assertSame("test description", $sensor->getDescription());
    }
}
