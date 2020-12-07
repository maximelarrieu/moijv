<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for($i = 0; $i < 60; $i++) {
            $game = new Game();

            $game->setName($faker->text(25));
            $game->setDateAdd($faker->dateTimeBetween('-2 years', 'now'));
            $game->setDescription($faker->text(300));
            $game->setUser($this->getReference('user'.random_int(0, UserFixtures::USER_COUNT - 1)));
            $game->setCategory($this->getReference('category' . random_int(0, count(CategoryFixtures::CATEGORIES) - 1)));
            for($j = 0; $j < random_int(0, 5); $j++) {
                $game->addTag($this->getReference('tag'.random_int(0, TagFixtures::TAG_COUNT - 1)));
            }
            $manager->persist($game);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TagFixtures::class
        ];
    }
}
