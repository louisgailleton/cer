<?php

namespace App\Repository;

use App\Entity\IndispoType;
use App\Entity\Moniteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IndispoType|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndispoType|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndispoType[]    findAll()
 * @method IndispoType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndispoTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndispoType::class);
    }


    
    public function indispoTypeMoniteur(Moniteur $moniteur)
    {
        return $this->createQueryBuilder('m')
                    ->where("m.moniteur = ?1")
                    ->setParameter(1, $moniteur)
                    ->getQuery()
                    ->getResult();
    }

    // /**
    //  * @return IndispoType[] Returns an array of IndispoType objects
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
    public function findOneBySomeField($value): ?IndispoType
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
