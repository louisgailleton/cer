<?php

namespace App\Repository;

use App\Entity\ContenuForfait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContenuForfait|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContenuForfait|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContenuForfait[]    findAll()
 * @method ContenuForfait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContenuForfaitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContenuForfait::class);
    }

    // Renvoie les contenus associés à un forfait
    public function contenusForfait($idForfait) {
        return $this->createQueryBuilder('c')
            ->join('c.Forfait', 'f')
            ->where('f.id = :idForfait')
            ->setParameter('idForfait', 13)
            ->orderBy('c.libelleContenu', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return ContenuForfait[] Returns an array of ContenuForfait objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContenuForfait
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
