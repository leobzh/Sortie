<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//use Symfony\Component\Security\Core\Password\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ProfileController extends AbstractController
{
    #[Route(path:"/profile", name:"profile_index")]
    public function index()
    {
        $user = $this->getUser();

        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirige
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileType::class, $user); // Crée le formulaire pour éditer le profil
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste l'utilisateur et applique les changements en base de données
            $entityManager->persist($user); // Persister l'entité
            $entityManager->flush(); // Sauvegarder les modifications en base de données

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/change-password', name: 'profile_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour changer votre mot de passe.');
            return $this->redirectToRoute('app_login');
        }

        // Créer le formulaire de changement de mot de passe
        $form = $this->createForm(ChangePasswordType::class, $user);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le nouveau mot de passe et la confirmation du mot de passe
            $newPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();

            // Vérifier que le nouveau mot de passe et la confirmation correspondent
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('profile_change_password');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

            // Mettre à jour le mot de passe de l'utilisateur (sans le hacher)
            $user->setPassword($hashedPassword);

            // Persister les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès !');

            // Rediriger l'utilisateur vers la page de profil après la mise à jour
            return $this->redirectToRoute('profile_index');
        }

        // Si le formulaire n'est pas soumis ou non valide, afficher le formulaire
        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


