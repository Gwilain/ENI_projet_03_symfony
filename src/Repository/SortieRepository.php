<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }



    public function findByFilters(?array $filters): array
    {

        $etats = [Etat::CODE_OUVERTE, Etat::CODE_EN_COURS, Etat::CODE_CLOTUREE, Etat::CODE_ANNULEE];

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.campus', 'c')
            ->addSelect('c')
            ->leftJoin('e.Etat', 'etat')
            ->addSelect('etat');

        if (!empty($filters['campus'])) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filters['campus']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('e.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('e.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filters['dateDebut']);
        }

        if (!empty($filters['dateFin'])) {
            $qb->andWhere('e.dateLimiteInscription <= :dateFin')
                ->setParameter('dateFin', $filters['dateFin']);
        }

        if (!empty($filters['sortiesQue']) && is_array($filters['sortiesQue'])) {
            $selected = $filters['sortiesQue'];

            if (in_array('organise', $selected)) {
//                $etats[] = Etat::CODE_EN_CREATION;
                $qb->andWhere('e.organisateur = :user');
            }

            if (in_array('inscrit', $selected)) {
                $qb->andWhere(':user MEMBER OF e.participants');
            }

            if (in_array('pasInscrit', $selected)) {
                $qb->andWhere(':user NOT MEMBER OF e.participants');
            }

            if (in_array('terminee', $selected)) {
                $etats[] = Etat::CODE_TERMINEE;
                $qb->andWhere('e.organisateur = :user');
            }

        }
            $qb->setParameter('user', $filters['user']);


        $qb->andWhere('(etat.code IN (:etatsPublies) OR (etat.code = :enCreation AND e.organisateur = :user))')
            ->setParameter('etatsPublies', $etats)
            ->setParameter('enCreation', Etat::CODE_EN_CREATION);

        $qb->orderBy('e.dateHeureDebut', 'ASC');

        return $qb->getQuery()->getResult();
    }


    /*
    public function findByFilters(?array $filters): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.campus', 'c')
            ->addSelect('c')
            ->leftJoin('e.Etat', 'etat')
            ->addSelect('etat');


        $qb->andWhere('etat.libelle IN (:etatsPublies)')
            ->setParameter('etatsPublies', ["ouverte", "En cours", "Cloturée"]);

        if (!empty($filters['campus'])) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filters['campus']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('e.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['dateDebut'])) {
            $qb->andWhere('e.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $filters['dateDebut']);
        }

        if (!empty($filters['dateFin'])) {
            $qb->andWhere('e.dateLimiteInscription <= :dateFin')
                ->setParameter('dateFin', $filters['dateFin']);
        }

        if (!empty($filters['sortiesQue']) && is_array($filters['sortiesQue'])) {
            $selected = $filters['sortiesQue'];

            if (in_array('organise', $selected)) {
                $qb->andWhere('e.organisateur = :user')
                    ->setParameter('user', $filters['user']);
            }

            if (in_array('inscrit', $selected)) {
                $qb->andWhere(':user MEMBER OF e.participants')
                    ->setParameter('user', $filters['user']);
            }

            if (in_array('pasInscrit', $selected)) {
                $qb->andWhere(':user NOT MEMBER OF e.participants')
                    ->setParameter('user', $filters['user']);
            }

            if (in_array('terminee', $selected)) {
                $qb->andWhere('etat.libelle = :etatTerminee')
                    ->setParameter('etatTerminee', "Terminée");
            }
        }

        // Si l'utilisateur est l'organisateur
        if (!empty($filters['user'])) {
            $qb->orWhere('e.organisateur = :user');
            if (!empty($filters['campus'])) {
                $qb->andWhere('e.campus = :campus');
            }
            $qb->setParameter('user', $filters['user']);
        }

        $qb->orderBy('e.dateHeureDebut', 'ASC');

        return $qb->getQuery()->getResult();
    }*/

    public function findToArchive(\DateTimeImmutable $dateLimite): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.Etat', 'e')
            ->andWhere('s.dateHeureDebut < :dateLimite')
            ->andWhere('e.code != :archiveCode')
            ->setParameter('dateLimite', $dateLimite)
            ->setParameter('archiveCode', \App\Entity\Etat::CODE_HISTORISEE)
            ->getQuery()
            ->getResult();
    }

    public function findSortiesDateLimiteDepassee(\DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.Etat', 'e')
            ->andWhere('s.dateLimiteInscription <= :now')
            ->andWhere('e.code != :cloturee')
            ->setParameter('now', $now)
            ->setParameter('cloturee', Etat::CODE_CLOTUREE)
            ->getQuery()
            ->getResult();
    }

}
