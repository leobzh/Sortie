<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieCreationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sorties = $entityManager->getRepository(Sortie::class)->findAll();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties
        ]);
    }

    // Creation d'une sortie
    #[Route('/sortieCreation', name: 'app_sortie_creation')]
    public function creation(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieCreationType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Sortie créée avec succès !');
            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('sortie/creation.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }
}
