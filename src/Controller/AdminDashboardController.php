<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/list_utilisateur', name: 'list_utilisateur')]
    public function listUserAdmin(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/listUtilisateur.html.twig', [
            'title' => 'Liste des utilisateurs',
            'utilisateurs' => $em->getRepository(Utilisateur::class)->findAllActive(),
        ]);
    }

    #[Route('/utilisateur/toggle/{id}', name: 'detail_utilisateur', methods: ['POST'])]
    public function toggleActif(EntityManagerInterface $em, Utilisateur $utilisateur): JsonResponse|Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $utilisateur->setIsActif(!$utilisateur->isActif());

        $em->persist($utilisateur);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'isActif' => $utilisateur->isActif(),
        ]);
    }

    #[Route('/utilisateur/delete/{id}', name: 'delete_utilisateur', methods: ['POST'])]
    public function softDeleteUtilisateur(Utilisateur $utilisateur, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('token'))) {
            $this->addFlash('danger', 'token invalide');
            return $this->redirectToRoute('admin_list_utilisateur');
        }

        $utilisateur->setDeleteAt(new \DateTimeImmutable('now'));
        $em->flush();

        $this->addFlash('success', 'BAN effectuer avec succes');

        return $this->redirectToRoute('admin_list_utilisateur');
    }

    #[Route('/create_utilisateur', name: 'create_utilisateur')]
    public function adminCreate(Request $request, UserPasswordHasherInterface $utilisateurPasswordHasher, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $utilisateur->setPassword($utilisateurPasswordHasher->hashPassword($utilisateur, $plainPassword));

            $utilisateur->setIsVerified(true);

            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('admin_list_utilisateur');
        }

        return $this->render('registration/register.html.twig', [
            'title' => 'Creation Utilisateur par Admin',
            'registrationForm' => $form,

        ]);
    }

    #[Route('/list_sortie', name: 'list_sortie')]
    public function listSortie(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/listSortie.html.twig', [
            'title' => 'Liste des sortie',
            'sorties' => $em->getRepository(Sortie::class)->findAll(),
        ]);
    }

    /*#[Route('/sortie/{id}/delete', name: 'delete_sortie', methods: ['POST'])]
    public function changeState(int $id, SortieRepository $sortieRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie introuvable');
        }
        $em->remove($sortie);
        $em->flush();

        return $this->redirectToRoute('admin_list_sortie');

    }*/

    #[Route('/sortie/{id}/cancelled', name: 'delete_sortie', methods: ['POST'])]
    public function annulationAdmin(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $etatClosed = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'CANCELLED']);

        if (!$etatClosed) {
            throw $this->createNotFoundException('L\'état "CLOSED" n\'existe pas.');
        }

        // Mettre à jour l'état de la sortie
        $sortie->setEtat($etatClosed);
        $em->flush();

        // Ajouter un message flash pour confirmer l'annulation
        $this->addFlash('success', 'La sortie a été annulée avec succès.');

        return $this->redirectToRoute('admin_list_sortie');
    }


}
