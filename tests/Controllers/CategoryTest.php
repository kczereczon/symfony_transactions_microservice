<?php

namespace App\Tests\Controllers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Traits\RefreshDatabase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CategoryTest extends ApiTestCase
{

    use RefreshDatabase;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPaginationStructure(): void
    {
        $response = static::createClient()->request('GET', '/category');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'pages' => 1,
            'page' => 1,
            'next' => '/category?page=1&limit=25',
            'previous' => '/category?page=1&limit=25',
            'total' => 0,
            'items' => [],
            'limit' => 25
        ]);
    }
}
