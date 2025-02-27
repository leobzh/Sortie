<?php

namespace App\DataFixtures;


use App\Entity\Site;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        $site1 = $this->getReference('site_0', Site::class);
        $site2 = $this->getReference('site_1', Site::class);


        $admin = new Utilisateur();
        $admin->setNom('Admin');
        $admin->setPrenom('Admin');
        $admin->setEmail('admin@campus-eni.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setSite($site1);
        $admin->setPseudo('Admin');
        $admin->setTelephone($faker->unique()->numerify('06 ## ## ## ##'));
        $admin->setIsAdministrateur(true);
        $admin->setIsActif(true);
        $admin->setIsVerified(true);

        $manager->persist($admin);
        $this->addReference('user_admin', $admin);


        for ($i = 1; $i <= 10; $i++) {
            $user = new Utilisateur();
            $user->setEmail($faker->unique()->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user' . $i));
            $user->setPseudo($faker->userName);
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setTelephone($faker->unique()->numerify('06 ## ## ## ##'));
            $user->setRoles(['ROLE_USER']);
            $user->setIsAdministrateur(false);
            $user->setIsActif($faker->boolean(90));
            $user->setSite($faker->randomElement([$site1, $site2]));

            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
        ];
    }
}