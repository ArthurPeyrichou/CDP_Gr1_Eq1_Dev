<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Member;
use App\Entity\Issue;
use App\Entity\PlanningPoker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanningPoker|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanningPoker|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanningPoker[]    findAll()
 * @method PlanningPoker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningPokerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanningPoker::class);
    }

    public function getPlanningPokerNotDoneByMember(Member $member): array
    {
        return $this->createQueryBuilder('p')
            ->Where('p.member = :member')
            ->andWhere('p.value < :val')
            ->setParameter('member', $member)
            ->setParameter('val', 0)
            ->getQuery()
            ->getResult();
    }

    public function getPlanningPokerByIssue(Issue $issue): array
    {
        return $this->createQueryBuilder('p')
            ->Where('p.issue = :issue')
            ->setParameter('issue', $issue)
            ->getQuery()
            ->getResult();
    }

    public function isPlanningPokerDoneByIssue(Issue $issue): bool
    {
        return empty($this->createQueryBuilder('p')
            ->Where('p.issue = :issue')
            ->andWhere('p.value < :val')
            ->setParameter('issue', $issue)
            ->setParameter('val', 0)
            ->getQuery()
            ->getResult());
    }

}
