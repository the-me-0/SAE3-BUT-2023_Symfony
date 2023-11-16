<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Entity\User;
use App\Entity\Facility;
use App\Entity\Room;

class SecurityTest extends WebTestCase
{
    public function testDashboardAccess(): void
    {
        $client = static::createClient();

        /* $link = $crawler->selectLink('Bâtiment 1')->link();
        $crawler = $client->click($link);*/

        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $admin = $userRepository->findOneBy(['username' => 'admin']);
        $tech = $userRepository->findOneBy(['username' => 'tech']);
        $sv = $userRepository->findOneBy(['username' => 'seeAll']);
        $visitor = $userRepository->findOneBy(['username' => 'visitor']);

        $this->testAccess($client, $admin, '/dashboard', TRUE);
        $this->testAccess($client, $tech, '/dashboard', FALSE);
        $this->testAccess($client, $sv, '/dashboard', TRUE);
        $this->testAccess($client, $visitor, '/dashboard', FALSE);
    }

    public function testEditAccess(): void
    {
        $client = static::createClient();

        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);
        $room = $entityManager->getRepository(Room::class)->findOneBy(['name' => 'D204']);

        $admin = $userRepository->findOneBy(['username' => 'admin']);
        $tech = $userRepository->findOneBy(['username' => 'tech']);
        $sv = $userRepository->findOneBy(['username' => 'seeAll']);
        $visitor = $userRepository->findOneBy(['username' => 'visitor']);

        $route = '/batiment-'.$room->getFacility()->getId().'/edit';

        $this->testAccess($client, $admin, $route, TRUE);
        $this->testAccess($client, $tech, $route, TRUE);
        $this->testAccess($client, $sv, $route, FALSE);
        $this->testAccess($client, $visitor, $route, FALSE);

        $route = '/batiment-'.$room->getFacility()->getId().'/salle-'.$room->getId().'/edit';

        $this->testAccess($client, $admin, $route, TRUE);
        $this->testAccess($client, $tech, $route, TRUE);
        $this->testAccess($client, $sv, $route, FALSE);
        $this->testAccess($client, $visitor, $route, FALSE);
    }

    public function testUserManagementAccess(): void
    {
        $client = static::createClient();

        $entityManager = self::$container->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);

        $admin = $userRepository->findOneBy(['username' => 'admin']);
        $tech = $userRepository->findOneBy(['username' => 'tech']);
        $sv = $userRepository->findOneBy(['username' => 'seeAll']);
        $visitor = $userRepository->findOneBy(['username' => 'visitor']);

        $route = '/admin';

        $this->testAccess($client, $admin, $route, TRUE);
        $this->testAccess($client, $tech, $route, FALSE);
        $this->testAccess($client, $sv, $route, FALSE);
        $this->testAccess($client, $visitor, $route, FALSE);
    }

    private function testAccess($client, User $user, string $route, bool $shouldPass) {
        $client->loginUser($user);

        $crawler = $client->request('GET', $route);

        $code = ($shouldPass) ? 200 : 403 ;

        /* As the /admin route is different, it has a special redirection when have right to access */
        if ($route == '/admin' && $shouldPass == TRUE)
            $code = 302;

        $this->assertSame($code, $client->getResponse()->getStatusCode());
    }
}
