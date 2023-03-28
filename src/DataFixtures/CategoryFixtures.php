<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'Health & Beauty',
                'color' => '#f11faa',
            ],
            [
                'name' => 'Loans',
                'color' => '#f11faa',
            ],
            [
                'name' => 'General',
                'color' => '#f11faa',
            ],
            [
                'name' => 'Food',
                'color' => '#f11faa',
            ],
            [
                'name' => 'Car & Transportation',
                'color' => '#f11faa',
            ],
            [
                'name' => 'Cloths',
                'color' => '#f11faa',
            ],
            [
                'name' => 'Other',
                'color' => '#f11faa',
            ]
        ];

        foreach ($categories as [$name, $color]) {
            $category = new Category();
            $category->setName($name);
            $category->setColor($color);
            $manager->persist($category);
            $manager->flush();
        }
    }
}
