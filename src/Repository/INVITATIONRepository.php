<?php

namespace App\Repository;

use App\Entity\INVITATION;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method INVITATION|null find($id, $lockMode = null, $lockVersion = null)
 * @method INVITATION|null findOneBy(array $criteria, array $orderBy = null)
 * @method INVITATION[]    findAll()
 * @method INVITATION[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class INVITATIONRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, INVITATION::class);
    }

    // /**
    //  * @return INVITATION[] Returns an array of INVITATION objects
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
    public function findOneBySomeField($value): ?INVITATION
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
