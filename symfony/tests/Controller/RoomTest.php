<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Entity\Room;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class RoomTest extends WebTestCase
{
    public function testRoomPage() {

        // Création d'un client HTTP pour tester l'application
        $client = static::createClient();

        // Récupération de l'entity manager
        $entityManager = self::$container->get('doctrine.orm.entity_manager');

        // Récupération du repository
        $roomRepository = $entityManager->getRepository(Room::class);

        // Récupération d'un produit par son id
        $room = $roomRepository->findOneBy(['name' => 'D204']);

        // Récupération du contenu de la réponse
        $crawler = $client->request('GET', '/batiment-'.$room->getFacility()->getId().'/salle-'.$room->getId());

        // Assertions pour vérifier que le contenu de la réponse contient les informations du produit
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p#room_num', 'D204');
    }
}
