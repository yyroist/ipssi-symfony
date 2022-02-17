<?php

namespace App\Repository;

use App\Entity\Prenom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prenom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prenom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prenom[]    findAll()
 * @method Prenom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrenomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prenom::class);
    }

    // /**
    //  * @return Prenom[] Returns an array of Prenom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Prenom
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
