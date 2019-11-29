<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Test|null find($id, $lockMode = null, $lockVersion = null)
 * @method Test|null findOneBy(array $criteria, array $orderBy = null)
 * @method Test[]    findAll()
 * @method Test[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    public function getProportionStatus(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.state) as count, t.state as value')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->groupBy('t.state')
            ->getQuery()
            ->getResult();
    }
}
