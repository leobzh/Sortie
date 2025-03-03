<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieCreationType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/sortie')]
final class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sorties = $entityManager->getRepository(Sortie::class)->findAll();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties
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

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()
        ) {
            // Ajoutez à nouveau l'utilisateur connecté pour vous assurer qu'il est inclus
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


    #[Route('/{id}/archive', name: 'app_sortie_archive', methods: ['GET', 'POST'])]
    public function archive(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        $etatArchive = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivé']);

        if (!$etatArchive) {
            throw $this->createNotFoundException('État "Archivé" non trouvé.');
        }

        $sortie->setEtat($etatArchive);
        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', 'La sortie a bien été archivée.');
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
