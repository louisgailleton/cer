<?php

namespace App\Repository;

use App\Entity\Eleve;
use App\Entity\ShortList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShortList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortList[]    findAll()
 * @method ShortList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortList::class);
    }

    public function findByEleve(Eleve $eleve)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.eleves', 'e')
            ->where('e.id = :eleve_id')
            ->setParameter('eleve_id', $eleve)
            ->getQuery()
            ->getResult();
    }

   

    // /**
    //  * @return ShortList[] Returns an array of ShortList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShortList
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
