<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Entity\Facility;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class FacilityTest extends WebTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFacilityList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/facility_list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h6', 'ID');
    }

    public function testFacilityListContent(): void
    {
        
        /* This part need the database to be set up with the "tests" fixtures's group */
        $client = static::createClient();
        $crawler = $client->request('GET', '/facility_list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h6#facilityName', 'Informatique-D');
    }

    public function testFacilityPage() {

        // Create a client to test the application
        $client = static::createClient();

        // Get the entity manager and the repository
        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $facilityRepository = $entityManager->getRepository(Facility::class);

        // Find the facility by it's name
        $facility = $facilityRepository->findOneBy(['name' => 'Informatique-D']);
        
        // make the request and get the content
        $crawler = $client->request('GET', '/batiment-'.$facility->getId());

        // Verify the content with the assertions
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('p.information_text', 'Informatique-D');
    }
}
