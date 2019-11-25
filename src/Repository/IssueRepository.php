<?php

namespace App\Repository;

use App\Entity\Issue;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Issue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Issue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Issue[]    findAll()
 * @method Issue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }

    public function getNextNumber(Project $project): int
    {
        $result = $this->createQueryBuilder('i')
            ->select('MAX(i.number) as maxNumber')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    public function getProportionStatus(Project $project): array
    {
        return $this->createQueryBuilder('i')
            ->select('Count(i.status) as count, i.status as value')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project)
            ->groupBy('i.status')
            ->getQuery()
            ->getResult();
    }

    public function getProportionPriority(Project $project): array
    {
        return $this->createQueryBuilder('i')
            ->select('Count(i.priority) as count, i.priority as value')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project)
            ->groupBy('i.priority')
            ->getQuery()
            ->getResult();
    }

    public function getProportionDifficulty(Project $project): array
    {
        return $this->createQueryBuilder('i')
            ->select('Count(i.difficulty) as count, i.difficulty as value')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project)
            ->groupBy('i.difficulty')
            ->getQuery()
            ->getResult();
    }
}
