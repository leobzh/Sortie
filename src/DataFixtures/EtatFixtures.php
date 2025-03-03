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
        /*
            created : etat initial de brouillon
            opened : la sortie est ouverte à la participation
            closed : les inscriptions sont fermées
            processing : la sortie a lieu actuellement
            terminated : la sortie a eu lieu (à la minute près)
            cancelled : la sortie a été annulée
        */

        $etats = [
            'CREATED', 'OPENED', 'CLOSED', 'PROCESSING', 'TERMINATED', 'CANCELLED', 'ARCHIVED'
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
