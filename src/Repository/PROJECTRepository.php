<?php

namespace App\Repository;

use App\Entity\PROJECT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PROJECT|null find($id, $lockMode = null, $lockVersion = null)
 * @method PROJECT|null findOneBy(array $criteria, array $orderBy = null)
 * @method PROJECT[]    findAll()
 * @method PROJECT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PROJECTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PROJECT::class);
    }

    // /**
    //  * @return PROJECT[] Returns an array of PROJECT objects
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
    public function findOneBySomeField($value): ?PROJECT
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
