<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $etats = [
            'OUVERT', 'EN_COURS', 'TERMINE', 'ANNULE'
        ];

        foreach ($etats as $i => $libelle) {
            $etat = new Etat();
            $etat->setLibelle($libelle);

            $manager->persist($etat);
            $this->addReference('etat_' . $i, $etat);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['etat'];
    }
}
