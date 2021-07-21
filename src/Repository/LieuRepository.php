<?php

namespace App\Repository;

use App\Entity\Lieu;
use App\Entity\Moniteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }



    public function LieuxMoniteur(Moniteur $moniteur)
    {
        return $this->createQueryBuilder('a')
            ->where("a.moniteur = ?1")
            ->orWhere("a.moniteur is NULL")
            ->andWhere("a.examen != 1")
            ->setParameter(1, $moniteur)
            ->getQuery()
            ->getResult();
    }

    public function LieuxExamen()
    {
        return $this->createQueryBuilder('a')
            ->where("a.examen = 1")
            ->getQuery()
            ->getResult();
    }

    public function Check(Moniteur $moniteur, Lieu $lieu)
    {
        return $this->createQueryBuilder('a')
            ->where("a.moniteur = ?1")
            ->andWhere('a.id = ?2')
            ->orWhere("a.moniteur is NULL")
            ->andWhere('a.id = ?2')
            ->setParameter(1, $moniteur)
            ->setParameter(2, $lieu)
            ->getQuery()
            ->getResult();
    }




    // /**
    //  * @return Lieu[] Returns an array of Lieu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lieu
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
