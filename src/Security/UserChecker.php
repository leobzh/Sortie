<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'a pas encore été vérifié. Veuillez vérifier vos emails.');
        }
        if (!$user->isActif()) {
            throw new CustomUserMessageAccountStatusException('Votre compte a été désactivé. Veuillez contacter l\'administrateur du site.');
        }

    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Tu peux ajouter d'autres vérifications ici après l'authentification si nécessaire
    }
}
