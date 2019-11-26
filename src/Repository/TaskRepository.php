<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
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

    public function getNextNumber(Project $project): int
    {
        $result = $this->createQueryBuilder('t')
            ->select('MAX(t.number) as maxNumber')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    /**
     * @return Task[]
     */
    public function getDone(Project $project): array
    {
        return $this->getByStatus($project, Task::DONE);
    }

    /**
     * @return Task[]
     */
    public function getDoing(Project $project): array
    {
        return $this->getByStatus($project, Task::DOING);
    }

    /**
     * @return Task[]
     */
    public function getToDo(Project $project): array
    {
        return $this->getByStatus($project, Task::TODO);
    }

    public function getProportionStatus(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.status) as count, t.status as value')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
    }

    public function getProportionEstimationManDays(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.requiredManDays) as count, t.requiredManDays as value')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->groupBy('t.requiredManDays')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMembersAssociated(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(dev.name) as count, dev.name as value')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMansDPerMembersAssociated(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(t.requiredManDays) as count, dev.name as value')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    private function getByStatus(Project $project, string $status): array
    {
        return $this->findBy([
            'project' => $project,
            'status' => $status
        ]);
    }
}
