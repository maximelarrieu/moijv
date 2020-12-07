<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        "Jeux PC",
        "Sony PS4",
        "Microsoft Xbox",
        "Nintendo Mario"
    ];

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $this->addReference('category'.$i, $category);
            $i++;
        }

        $manager->flush();
    }
}
