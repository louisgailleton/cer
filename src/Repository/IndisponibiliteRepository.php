<?php

namespace App\Repository;

use App\Entity\Indisponibilite;
use App\Entity\Moniteur;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Indisponibilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indisponibilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indisponibilite[]    findAll()
 * @method Indisponibilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indisponibilite::class);
    }

    public function Indispo(Moniteur $moniteur)
    {
        return $this->createQueryBuilder('m')
                    ->where("m.moniteur = ?1")
                    ->setParameter(1, $moniteur)
                    ->getQuery()
                    ->getResult();
    }




 

    // /**
    //  * @return Indisponibilite[] Returns an array of Indisponibilite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Indisponibilite
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
