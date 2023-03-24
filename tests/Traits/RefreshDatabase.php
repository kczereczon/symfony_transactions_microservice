<?php

namespace App\Tests\Traits;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

trait RefreshDatabase
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
        $options = array('command' => 'doctrine:schema:drop', '--full-database' => true, '--force' => true);
        $application->run(new ArrayInput($options));
    }
}