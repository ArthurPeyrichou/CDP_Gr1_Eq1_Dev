<?php

namespace App\Repository;

use App\Entity\Sprint;
use App\Entity\Task;
use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getNextNumber(Sprint $sprint): int
    {
        $result = $this->createQueryBuilder('t')
            ->select('MAX(t.number) as maxNumber')
            ->andWhere('t.sprint= :sprint')
            ->setParameter('sprint', $sprint)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    /**
     * @return Task[]
     */
    public function getDone(Sprint $sprint): array
    {
        return $this->getByStatus($sprint, Task::DONE);
    }

    /**
     * @return Task[]
     */
    public function getDoing(Sprint $sprint): array
    {
        return $this->getByStatus($sprint,Task::DOING);
    }

    /**
     * @return Task[]
     */
    public function getToDo(Sprint $sprint): array
    {
        return $this->getByStatus($sprint, Task::TODO);
    }

    public function getProportionStatus(Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.status) as count, t.status as value')
            ->andWhere('t.sprint= :sprint')
            ->setParameter('sprint', $sprint)
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
    }

    public function getProportionEstimationManDays(Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.requiredManDays) as count, t.requiredManDays as value')
            ->andWhere('t.sprint= :sprint')
            ->setParameter('sprint', $sprint)
            ->groupBy('t.requiredManDays')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMembersAssociated(Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(dev.name) as count, dev.name as value')
            ->andWhere('t.sprint= :sprint')
            ->setParameter('sprint', $sprint)
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMansDPerMembersAssociated(Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(t.requiredManDays) as count, dev.name as value')
            ->andWhere('t.sprint= :sprint')
            ->setParameter('sprint', $sprint)
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    public function getProportionStatusByIssue(Issue $issue): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('Count(t.status) as count, t.status as value')
            ->groupBy('t.status');
        return $qb->join('t.relatedIssues', 'i')
            ->where($qb->expr()->eq('i.id', $issue->getId()))
            ->getQuery()
            ->getResult();
    }

    public function getProportionEstimationManDaysByIssue(Issue $issue): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('Count(t.requiredManDays) as count, t.requiredManDays as value')
            ->groupBy('t.requiredManDays');
            return $qb->join('t.relatedIssues', 'i')
                ->where($qb->expr()->eq('i.id', $issue->getId()))
                ->getQuery()
                ->getResult();
    }

    public function getProportionMembersAssociatedByIssue(Issue $issue): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('Count(dev.name) as count, dev.name as value')
            ->groupBy('t.developper')
            ->join('t.developper', 'dev');
        return $qb->join('t.relatedIssues', 'i')
            ->where($qb->expr()->eq('i.id', $issue->getId()))
            ->getQuery()
            ->getResult();
    }

    public function getProportionMansDPerMembersAssociatedByIssue(Issue $issue): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.requiredManDays) as count, dev.name as value')
            ->groupBy('t.developper')
            ->join('t.developper', 'dev');
        return $qb->join('t.relatedIssues', 'i')
            ->where($qb->expr()->eq('i.id', $issue->getId()))
            ->getQuery()
            ->getResult();
    }

    private function getByStatus(Sprint $sprint, string $status): array
    {
        return $this->findBy([
            'sprint' => $sprint,
            'status' => $status
        ]);
    }
}
