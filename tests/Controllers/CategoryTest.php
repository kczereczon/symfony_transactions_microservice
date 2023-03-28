<?php

namespace App\Tests\Controllers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Tests\Traits\RefreshDatabase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Faker\Factory;
use Faker\Generator;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
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
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrate();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class);
        $this->faker = Factory::create();
        $this->purger = new ORMPurger($this->entityManager);
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
        foreach (range(1, 100) as $i) {
            $category = new Category();
            $category->setName($this->faker->domainName());
            $category->setColor($this->faker->colorName());
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        static::createClient()->request('GET', '/category');

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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->purger->purge();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
