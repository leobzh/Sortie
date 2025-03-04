<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieCreationType;
use App\Repository\EtatRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/sortie')]
final class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie', methods: ['GET', 'POST'])]
    public function recherche(
        Request $request,
        SortieRepository $sortiesRepository,
        SiteRepository $siteRepository,
        EtatRepository $etatRepository
    ): Response {
        $sites = $siteRepository->findAll();
        $selectedSiteId = $request->request->get('site');
        $nom = $request->request->get('nom');
        $dateHeureDebut = $request->request->get('dateHeureDebut');
        $dateLimiteInscription = $request->request->get('dateLimiteInscription');
        $isOrganisateur = $request->request->get('isOrganisateur');
        $isInscrit = (bool)$request->request->get('isInscrit');
        $isNotInscrit = (bool)$request->request->get('isNotInscrit');
        $isEnded = (bool)$request->request->get('isEnded');

        // Construire les critères dynamiquement
        $criteria = [];
        if ($selectedSiteId) {
            $criteria['site'] = (int)$selectedSiteId; // Convertir en int pour Doctrine
        }
        if ($isOrganisateur) {
            $criteria['organisateur'] = $this->getUser(); // YG : on passe l'id du connected user
        }

        // Appliquer les filtres si au moins un critère ou filtre manuel est défini
        if (!empty($criteria) || $nom || $dateHeureDebut || $dateLimiteInscription || $isInscrit || $isNotInscrit || $isEnded) {
            $sorties = $sortiesRepository->findByFilters($isInscrit, $isNotInscrit, $isEnded, $nom, $dateHeureDebut, $dateLimiteInscription, $criteria);
        } else {
            // Par défaut, toutes les sorties si pas de filtre
            $sorties = $sortiesRepository->findAll();
        }

        // Récupérer les libellés des états
        foreach ($sorties as $sortie) {
            $etat = $etatRepository->find($sortie->getEtat()->getId());
            $sortie->etatLibelle = $etat ? $etat->getLibelle() : 'Inconnu';
        }

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
            'selectedSiteId' => $selectedSiteId,
            'nom' => $nom,
            'dateHeureDebut' => $dateHeureDebut,
            'dateLimiteInscription' => $dateLimiteInscription,
            'isOrganisateur' => $isOrganisateur,
            'isInscrit' => $isInscrit,
            'isNotInscrit' => $isNotInscrit,
            'isEnded' => $isEnded,
        ]);
    }



    // Creation d'une sortie
    #[Route('/Creation', name: 'app_sortie_creation')]
    public function creation(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->addParticipant($this->getUser()); // Ajoute l'utilisateur connecté comme participant

        $sortieForm = $this->createForm(SortieCreationType::class, $sortie);
        $sortieForm->handleRequest($request);

       if ($sortieForm->isSubmitted() && !$sortieForm->isValid()) {
            dump($sortieForm->getErrors(true, false));
        }


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()
        ) {
            // Ajoutez à nouveau l'utilisateur connecté pour être sûr qu'il est inclus
            $sortie->addParticipant($this->getUser());

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success',
                'Sortie créée avec succès !'
            );
            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('sortie/creation.html.twig', [
            'sortieForm' => $sortieForm,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_details', methods: ['GET'])]
    public function details(Sortie $sortie): Response
    {
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        $sortieForm = $this->createForm(SortieCreationType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            // Persister les changements
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée');
            return $this->redirectToRoute('app_sortie_details', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    // annuler une sortie
    #[Route('/{id}/annuler', name: 'app_sortie_annulation', methods: ['POST'])]
    public function annulation(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        // Récupérer l'état "CANCELLED"
        $etatClosed = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'CANCELLED']);

        if (!$etatClosed) {
            throw $this->createNotFoundException('L\'état "CLOSED" n\'existe pas.');
        }

        // Mettre à jour l'état de la sortie
        $sortie->setEtat($etatClosed);
        $em->flush();

        // Ajouter un message flash pour confirmer l'annulation
        $this->addFlash('success', 'La sortie a été annulée avec succès.');

        return $this->redirectToRoute('app_sortie_details', ['id' => $sortie->getId()]);
    }

    // archiver une sortie
    #[Route('/{id}/archiver', name: 'app_sortie_archive', methods: ['POST'])]
    public function archive(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        // Récupérer l'état "ARCHIVED"
        $etatClosed = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'ARCHIVED']);

        if (!$etatClosed) {
            throw $this->createNotFoundException('L\'état "ARCHIVED" n\'existe pas.');
        }

        // Mettre à jour l'état de la sortie
        $sortie->setEtat($etatClosed);
        $em->flush();

        // Ajouter un message flash pour confirmer l'annulation
        $this->addFlash('success', 'La sortie a été annulée avec succès.');

        return $this->redirectToRoute('app_sortie_details', ['id' => $sortie->getId()]);
    }


    #[Route('/{id}/participer', name: 'app_sortie_participer', methods: ['GET', 'POST'])]
    public function participer(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $sortie->addParticipant($user);
        $em->flush();

        return $this->redirectToRoute('app_sortie_details', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/{id}/remove-participant/{userId}', name: 'app_sortie_remove_participant', methods: ['POST'])]
    public function removeParticipant(int $id, int $userId, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $entityManager->getRepository(Utilisateur::class)->find($userId);

        if (!$sortie || !$user) {
            throw $this->createNotFoundException('Sortie ou utilisateur introuvable.');
        }

        if ($sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sortie_details', ['id' => $sortie->getId()]);
    }


    #[Route('/sortie/{id}/delete', name: 'app_sortie_remove', methods: ['GET', 'POST'])]
    public function delete(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie introuvable.');
        }

        $entityManager->remove($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sortie');
    }

}
