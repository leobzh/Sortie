<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    public function edit(Request $request, UtilisateurRepository $userRepository): Response
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
            // Sauvegarde les modifications du profil
            $userRepository->save($user, true);
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/change-password', name: 'profile_change_password')]
    public function changePassword(Request $request, UtilisateurRepository $userRepository)
{
    $user = $this->getUser(); // Récupère l'utilisateur connecté

    // Crée le formulaire pour changer le mot de passe
    $form = $this->createForm(ChangePasswordType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupère les données du formulaire
        $oldPassword = $form->get('old_password')->getData();
        $newPassword = $form->get('new_password')->getData();

        // Vérifier que l'ancien mot de passe est correct
        if (password_verify($oldPassword, $user->getPassword())) {
            // Appliquer le nouveau mot de passe en utilisant la méthode du repository
            $userRepository->upgradePassword($user, $newPassword);  // Utilise la méthode upgradePassword

            // Sauvegarde les changements dans la base de données
            $userRepository->save($user, true);

            // Message de succès
            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès !');
            return $this->redirectToRoute('profile_index');  // Redirige vers le profil
        } else {
            // Message d'erreur si l'ancien mot de passe est incorrect
            $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
        }
    }

    return $this->render('profile/change_password.html.twig', [
        'form' => $form->createView(),
    ]);
}
}

