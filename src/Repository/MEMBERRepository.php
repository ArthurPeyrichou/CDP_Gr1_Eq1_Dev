<?php

namespace App\Repository;

use App\Entity\MEMBER;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MEMBER|null find($id, $lockMode = null, $lockVersion = null)
 * @method MEMBER|null findOneBy(array $criteria, array $orderBy = null)
 * @method MEMBER[]    findAll()
 * @method MEMBER[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MEMBERRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MEMBER::class);
    }

    // /**
    //  * @return MEMBER[] Returns an array of MEMBER objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MEMBER
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
