<?php

namespace App\Tests;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;

class TransactionTest extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        // Call the parent setup method
        parent::setUp();

        // Run database migrations
        $kernel = static::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = array('command' => 'doctrine:migrations:migrate', '--no-interaction' => true);
        $application->run(new ArrayInput($options));
    }

    public function testPagination(): void
    {
        $response = static::createClient()->request('GET', '/transaction');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'page' => 1,
            'pages' => 1,
            'transactions' => [],
            'limit' => 25,
            'next' => '/transaction?page=1&limit=25',
            'previous' => '/transaction?page=1&limit=25',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        // Call the parent teardown method
        parent::tearDown();

        // Drop the database
        $kernel = static::createKernel();
        $kernel->boot();
        // Close all connections to the database
        $kernel = static::createKernel();
        $kernel->boot();
        /** @var Connection $connection */
        $connection = $kernel->getContainer()->get('doctrine')->getConnection();
        $connection->close();

        sleep(5);

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = array('command' => 'doctrine:schema:drop', '--force' => true);
        $application->run(new ArrayInput($options));
    }
}
