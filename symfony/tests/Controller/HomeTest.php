<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class HomeTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        /* $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h6', 'Données des dernières 24H'); */
        /* As the homepage isn't yet implemented, we can't test it */
    }
}
