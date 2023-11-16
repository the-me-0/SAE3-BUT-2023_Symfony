<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

use App\Domain\Query\DataQuery;
use App\Domain\Query\DataHandler;

class DataTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function testDataQuery(): void
    {
        /* providing bad dates */
        $this->expectException(\Exception::class);
        new DataQuery(1, '', 0, 'this isn\'t a date', 'i\'m not either');
    }

    public function testDataHandler(): void
    {
        /* Create the DataQuery */
        $query = new DataQuery(4, '', 10, '', '');

        $registry = self::$container->get('doctrine.orm.manager_registry');

        $handler = new DataHandler($registry);
        $this->assertEquals([], $handler->getErrors());

        /* Don't know what to test on this */
    }
}
