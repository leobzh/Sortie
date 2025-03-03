<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/utilisateur', name: 'utilisateur_')]
#[isGranted('ROLE_USER')]
final class UtilisateurController extends AbstractController
{

    #[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, Request $request, UtilisateurRepository $utilisateurRepository, Utilisateur $participant): Response
    {
        $sortieId = $request->query->get('sortieId');
        $sorties = $participant->getSorties();

        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé !');
        }




        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
            'sortieId' => $sortieId,
            'participant' => $participant,
            'sorties' => $sorties

        ]);
    }
}
/* if (!$participant) {
                $this->addFlash('error', 'Utilisateur introuvable.');
                return $this->redirectToRoute('utilisateur_list');
       }*/