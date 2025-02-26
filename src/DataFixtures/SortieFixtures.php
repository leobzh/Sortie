<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $nbSorties = 8;

        for ($i = 0; $i < $nbSorties; $i++) {
            $site = $this->getReference('site_' . rand(0, 1), Site::class);
            $lieu = $this->getReference('lieu_' . rand(0, 9), Lieu::class);
            $etat = $this->getReference('etat_' . rand(0, 3), Etat::class);
            $organisateur = $this->getReference('user_' . rand(1, 10), Utilisateur::class);

            $dateDebut = $faker->dateTimeBetween('+3 days', '+2 months');
            $dateLimite = (clone $dateDebut)->modify('-3 days');

            $sortie = new Sortie();
            $sortie->setNom($faker->sentence(3));
            $sortie->setDateHeureDebut($dateDebut);
            $sortie->setDuree($faker->numberBetween(60, 300));
            $sortie->setDateLimiteInscription($dateLimite);
            $sortie->setNbInscriptionsMax($faker->numberBetween(5, 20));
            $sortie->setInfosSortie($faker->paragraph());

            $sortie->setSite($site);
            $sortie->setLieu($lieu);
            $sortie->setEtat($etat);
            $sortie->setOrganisateur($organisateur);

            $nbParticipants = rand(3, min(10, $sortie->getNbInscriptionsMax()));
            $participants = [];
            for ($j = 0; $j < $nbParticipants; $j++) {
                do {
                    $participant = $this->getReference('user_' . rand(1, 10), Utilisateur::class);
                } while (in_array($participant, $participants));

                $participants[] = $participant;
                $sortie->addParticipant($participant);
            }

            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
            UtilisateurFixtures::class,
            LieuFixtures::class,
            EtatFixtures::class
        ];
    }
}
