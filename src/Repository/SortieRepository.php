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
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', 3);
        }


        // Filtre sur les critères dynamiques (ex. site, état, etc.) avec valeur EXACTES
        foreach ($criteria as $field => $value) {
            if (!empty($value)) { // Évite les filtres avec valeurs vides
                $qb->andWhere("s.$field = :$field")
                    ->setParameter($field, $value);
            }
        }


        return $qb->getQuery()->getResult();
    }
}