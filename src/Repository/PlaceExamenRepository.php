<?php

namespace App\Repository;

use App\Entity\Lieu;
use App\Entity\Moniteur;
use App\Entity\PlaceExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaceExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceExamen[]    findAll()
 * @method PlaceExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceExamen::class);
    }

    public function checkExistingPlace(Moniteur $moniteur, Lieu $lieu)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.moniteur = :moniteur')
            ->andWhere('p.lieu = :lieu')
            ->setParameter('moniteur', $moniteur)
            ->setParameter('lieu', $lieu)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getSingleScalarResult();
    }


    


    public function findByMoniteur(Moniteur $moniteur)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.moniteur = :val')
            ->setParameter('val', $moniteur)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
