<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }

    #[Route('/import-users', name: 'import_users')]
    public function importUsers(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('csv_file');

            if ($file) {
                $handle = fopen($file->getPathname(), 'r');

                // Lire la première ligne (en-tête)
                fgetcsv($handle);

                $i = 0;
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    // email,roles,password,pseudo,nom,prenom,site

                    $email = $data[0];
                    $role = $data[1] ?? 'ROLE_USER';
                    $password = $data[2];
                    $pseudo = $data[3];
                    $nom = $data[4];
                    $prenom = $data[5];
                    $site = $data[6];

                    // Vérifier si le mail de l'utilisateur existe déjà
                    $existingEmail = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                    if ($existingEmail) {
                        continue;
                    }
                    // Vérifier si le pseudo de l'utilisateur existe déjà
                    $existingPseudo = $entityManager->getRepository(Utilisateur::class)->findOneBy(['pseudo' => $pseudo]);
                    if ($existingPseudo) {
                        continue;
                    }
                    // Vérifier si le site existe
                    $site = $entityManager->getRepository(Site::class)->find($site);
                    if (!$site) {
                        continue;
                    }

                    // Créer un nouvel utilisateur VERIFIE

                    $i++;

                    // email,roles,password,pseudo,nom,prenom,site
                    $user = new Utilisateur();
                    $user->setEmail($email);
                    $user->setRoles([$role]);
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                    $user->setPseudo($pseudo);
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
                    $user->setSite($site);

                    /* Ici on active directement son compte
                    mais on pourrait décider de pas le faire et de lui envoyer un email de confirmation de compte
                    */
                    $user->setIsVerified(true);



                    $entityManager->persist($user);
                }

                fclose($handle);
                $entityManager->flush();

                $this->addFlash('success', "Utilisateurs importés avec succès ($i)");
                return $this->redirectToRoute('admin_import_users');
            }
        }

        return $this->render('admin/import_users.html.twig', []);
    }

    #[Route('/', name: 'dashboard')]
    public function isActif(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }
}
