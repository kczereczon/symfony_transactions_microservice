<?php

namespace App\Tests\Controllers;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Faker\Factory;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SubcategoryControllerTest extends ApiTestCase
{

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
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
    public function testPagination(): void
    {
        foreach (range(1, 25) as $i) {
            $category = new Category();
            $category->setName($this->faker->domainName());
            $category->setColor($this->faker->colorName());
            $this->entityManager->persist($category);

            foreach (range(1, 4) as $j) {
                $subcategory = new SubCategory();
                $subcategory->setName($this->faker->domainName());
                $subcategory->setCategory($category);
                $this->entityManager->persist($subcategory);
            }
        }


        $this->entityManager->flush();

        $response = static::createClient()->request('GET', '/subcategory');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'pages' => 4,
            'page' => 1,
            'next' => '/subcategory?page=2&limit=25',
            'previous' => '/subcategory?page=1&limit=25',
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
