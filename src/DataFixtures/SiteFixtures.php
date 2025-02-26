<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $site = new Site();
            $site->setNom($faker->word);

            $manager->persist($site);
            $this->addReference('site_' . $i, $site);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['site'];
    }
}
