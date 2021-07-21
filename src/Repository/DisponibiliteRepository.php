<?php

namespace App\Repository;

use App\Entity\Disponibilite;
use App\Entity\Eleve;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Disponibilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disponibilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disponibilite[]    findAll()
 * @method Disponibilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilite::class);
    }


    public function Eleve(Eleve $eleve)
    {
        return $this->createQueryBuilder('m')
            ->where("m.eleve = ?1")
            ->setParameter(1, $eleve)
            ->getQuery()
            ->getResult();
    }


    //a changer parcourir toutes les dispos du jour en cours
    public function Dispo(DateTime $start)
    {
        $from = new \DateTime($start->format("Y-m-d") . " 00:00:00");
        $to   = new \DateTime($start->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder("e");
        $qb
            ->Where('e.start BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    // /**
    //  * @return Disponibilite[] Returns an array of Disponibilite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Disponibilite
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
