<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ville;
use Faker\Factory;

class VilleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);

            $manager->persist($ville);
            $this->addReference('ville_' . $i, $ville);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['ville'];
    }

}
