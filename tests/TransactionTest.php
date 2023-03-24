<?php

namespace App\Tests;

use App\Tests\Traits\RefreshDatabase;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;

class TransactionTest extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    use RefreshDatabase;

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
}
