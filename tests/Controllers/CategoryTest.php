<?php

namespace App\Tests\Controllers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Tests\Traits\RefreshDatabase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Faker\Factory;
use Faker\Generator;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CategoryTest extends ApiTestCase
{

    use RefreshDatabase;

    private ?EntityManager $entityManager;
    private Generator $faker;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->migrate();

        $kernel = static::createKernel();
        $kernel->boot();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->faker = Factory::create();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ORMException
     */
    public function testPaginationStructure(): void
    {
        $response = static::createClient()->request('GET', '/category');

        foreach (range(0, 100) as $item) {
            $category = new Category();
            $category->setColor($this->faker->hexColor);
            $category->setName($this->faker->name);
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'pages' => 4,
            'page' => 1,
            'next' => '/category?page=2&limit=25',
            'previous' => '/category?page=1&limit=25',
            'total' => 100,
            'limit' => 25
        ]);
    }

    public function tearDown(): void
    {
        $this->clear();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
