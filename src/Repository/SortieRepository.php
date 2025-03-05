<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class SortieRepository extends ServiceEntityRepository
{

    private mixed $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security = $security;
    }

    public function findBySiteAndName(int $siteId, string $nom): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.site = :site')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('site', $siteId)
            ->setParameter('nom', '%' . $nom . '%')
            ->getQuery()
            ->getResult();
    }

    /*
     * Liste les sorties qui viennent de démarrer
     * PROCESSING -> quand la sortie a debutée ET n'est pas finie
     */
    public function findSortiesEnCours()
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateHeureDebut <= :now') // La date de début est passée ou c'est maintenant
            ->andWhere('s.dateHeureDebut + s.duree > :now') // La date de fin n'est pas encore passée
            ->leftJoin('s.etat', 'e')
            ->andWhere('e.libelle = :etat')
            ->setParameter('etat', 'OPENED')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getResult();
    }

    /*
         * Liste les sorties à archiver
         * ARCHIVED -> quand la sortie est terminée depuis plus de 1 mois
         */
    public function findSortiesArchivees()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('DATE_ADD(s.dateHeureDebut, s.duree, \'minute\') < :oneMonthAgo') // L'événement est terminé depuis +1 mois
            ->leftJoin('s.etat', 'e')
            ->andWhere('e.libelle != :archived') // On exclut ceux déjà archivés
            ->setParameter('archived', 'ARCHIVED')
            ->setParameter('oneMonthAgo', (new \DateTime())->modify('-1 month'))
            ->getQuery()
            ->getResult();
    }

    /*
         * Liste les sorties à fermer (inscriptions closes)
         * CLOSED -> quand la date d'inscription de la sortie est passée
         */
    public function findSortiesFermees()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateLimiteInscription < :now ') // La date d'inscription est derrière nous
            ->leftJoin('s.etat', 'e')
            ->andWhere('e.libelle != :closed') // On exclut ceux déjà closed
            ->setParameter('now', new \DateTime('now'))
            ->setParameter('closed', 'CLOSED')
            ->getQuery()
            ->getResult();
    }

    /*
         * Liste les sorties dont la date de
         * TERMINATED-> quand la sortie est finie
         */
    public function findSortiesTerminees()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut + s.duree < :now ') // La date de la sortie + sa duree est inferieure à aujourd'hui
            ->leftJoin('s.etat', 'e')
            ->andWhere('e.libelle != :terminated') // On exclut ceux déjà terminated
            ->setParameter('now', new \DateTime('now'))
            ->setParameter('terminated', 'TERMINATED')
            ->getQuery()
            ->getResult();
    }

    public function findByNameContaining(string $nom): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('nom', '%' . $nom . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(bool $isInscrit, bool $isNotInscrit, bool $isEnded, ?string $nom = null, ?string $dateHeureDebut = null, ?string $dateLimiteInscription = null, array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('s');

        // Filtre sur l'inscription (si la checkbox est cochée)

        /* VERSION EN JOIN

        if ($isInscrit) {
            $user = $this->security->getUser();
            if ($user instanceof Utilisateur) {

                $qb->join('s.participants', 'p')
                    ->andWhere('p.id = :userId')
                    ->setParameter('userId', $user->getId());
            }
        }

        */
        /* VERSION AVEC MEMBER OF */

        if ($isInscrit) {
            $user = $this->security->getUser();
            if ($user instanceof Utilisateur) {
                $qb->andWhere(':user MEMBER OF s.participants')
                    ->setParameter('user', $user);
            }
        }

        // Filtre sur non-inscription (si la checkbox est cochée)
        if ($isNotInscrit) {
            $user = $this->security->getUser();
            if ($user instanceof Utilisateur) {
                $qb->andWhere(':user NOT MEMBER OF s.participants')
                    ->setParameter('user', $user);
            }
        }


        // Filtre sur le nom
        if ($nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        // Filtre sur dateHeureDebut
        if ($dateHeureDebut) {
            $qb->andWhere('s.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', new \DateTime($dateHeureDebut));
        }

        // Filtre sur dateLimiteInscription
        if ($dateLimiteInscription) {
            $qb->andWhere('s.dateLimiteInscription <= :dateLimiteInscription')
                ->setParameter('dateLimiteInscription', new \DateTime($dateLimiteInscription));
        }

        // Filtre sur isEnded
        if ($isEnded) {
            $qb = $this->createQueryBuilder('s')
                ->leftJoin('s.etat', 'e')
                ->andWhere('e.libelle = :etat')
                ->setParameter('etat', 'TERMINATED');
        }

        // Filtre sur les critères dynamiques (site, organisateur) avec valeur EXACTES
        foreach ($criteria as $field => $value) {
            if (!empty($value)) { // Évite les filtres avec valeurs vides
                $qb->andWhere("s.$field = :$field")
                    ->setParameter($field, $value);
            }
        }


        return $qb->getQuery()->getResult();
    }
}