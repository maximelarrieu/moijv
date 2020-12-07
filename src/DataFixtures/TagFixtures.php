<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TagFixtures extends Fixture
{
    const TAG_COUNT = 180;
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for($i = 0; $i<self::TAG_COUNT; $i++) {
            $tag = new Tag();
            $tag->setName($faker->unique()->words(random_int(1, 3), true));
            $this->addReference('tag'.$i, $tag);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
