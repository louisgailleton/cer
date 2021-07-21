<?php

namespace App\Repository;

use App\Entity\Agence;
use App\Entity\Forfait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Forfait|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forfait|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forfait[]    findAll()
 * @method Forfait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForfaitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forfait::class);
    }

    // Renvoie les forfaits associés à une agence
    public function forfaitAgence($idAgence) {
        return $this->createQueryBuilder('f')
            ->join('f.agence', 'a')
            ->where('a.id = :idAgence')
            ->setParameter('idAgence', $idAgence)
            ->orderBy('f.libelleforfait', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Forfait[] Returns an array of Forfait objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Forfait
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
