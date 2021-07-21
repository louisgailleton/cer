<?php

namespace App\Repository;

use App\Entity\StatutSocial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StatutSocial|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutSocial|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutSocial[]    findAll()
 * @method StatutSocial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutSocialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutSocial::class);
    }

    // /**
    //  * @return StatutSocial[] Returns an array of StatutSocial objects
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
    public function findOneBySomeField($value): ?StatutSocial
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
