<?php

namespace App\Repository;

use App\Entity\Eleve;
use App\Entity\Lieu;
use App\Entity\Moniteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleve[]    findAll()
 * @method Eleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    public function findEleveByString($str)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('CONCAT(CONCAT(e.nom, \' \'), e.prenom) LIKE :str')
            ->orWhere('CONCAT(CONCAT(e.prenom, \' \'), e.nom) LIKE :str')
            ->setParameter('str', "%" . $str . "%")
            ->orderBy('e.nom', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    public function findCompteur($eleve)
    {
        return $this->createQueryBuilder('e')
            ->select('e.compteurHeure')
            ->where('e.id = :eleve')
            ->setParameter('eleve', $eleve)
            ->getQuery()
            ->getResult();
    }

    public function countEleveSlLieu(Moniteur $moniteur, Lieu $lieu) //retourner le nb d'eleve pour le moniteur et le lieu passé
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.shortList', 's')
            ->innerJoin('s.moniteurs', 'm')
            ->where('e.lieu = :lieu')
            ->andWhere('m.id = :moniteur')
            ->setParameter('moniteur', $moniteur)
            ->setParameter('lieu', $lieu)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countEleveLieu(Lieu $lieu) //retourner le nb d'eleve pour le lieu passé
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.lieu = :lieu')
            ->setParameter('lieu', $lieu)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findSucceedByMoniteur()
    {
        return $this->createQueryBuilder('e')
            ->select('IDENTITY(e.soumis_par) as id, count(e.id) as nbReussite')
            ->where('e.examen_Reussi = true')
            ->groupBy('e.soumis_par')
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Eleve[] Returns an array of Eleve objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Eleve
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
