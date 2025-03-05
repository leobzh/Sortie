<?php
// src/EventSubscriber/EventStatusSubscriber.php
namespace App\EventSubscriber;

use App\Entity\Etat;
use App\Event\EventCheckEvent;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class EventStatusSubscriber implements EventSubscriberInterface
{

    private $eventRepository;
    private $etatRepository;
    private $entityManager;
    private $logger;

    public function __construct(
        SortieRepository       $eventRepository,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface        $logger
    )
    {
        $this->eventRepository = $eventRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Déclare quel(s) événement(s) ce subscriber écoute
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EventCheckEvent::NAME => 'onEventCheck',
        ];
    }

    public function setEtatSortie($sorties, $etatLibelle): void {

        $this->logger->info("CHECK $etatLibelle " . count($sorties) . ' événement(s) à mettre à jour.');
        // Met à jour chaque événement
        $etat = $this->etatRepository->findOneBy(['libelle' => "$etatLibelle"]);
        foreach ($sorties as $sortie) {
            $oldStatus = $sortie->getEtat()->getLibelle();

            $sortie->setEtat($etat);
            $this->logger->info(sprintf(
                'Événement "%s" (ID: %d) : état changé de "%s" à "%s".',
                $sortie->getNom(),
                $sortie->getId(),
                $oldStatus,
                $etat->getLibelle()
            ));
        }
        // Sauvegarde les changements en base de données
        if (count($sorties) > 0) {
            $this->entityManager->flush();
            $this->logger->info('Changements sauvegardés en base de données.');
        }
    }

    /**
     * Méthode exécutée lorsque l'événement event.check est déclenché
     */
    public function onEventCheck(EventCheckEvent $event): void
    {
        $this->setEtatSortie($this->eventRepository->findSortiesEnCours(), 'PROCESSING');
        $this->setEtatSortie($this->eventRepository->findSortiesArchivees(), 'ARCHIVED');
        $this->setEtatSortie($this->eventRepository->findSortiesFermees(), 'CLOSED');
        $this->setEtatSortie($this->eventRepository->findSortiesTerminees(), 'TERMINATED');
    }
}