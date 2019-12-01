<?php

namespace App\Repository;

use App\Entity\Project;

use App\Entity\Sprint;
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

    public function getNextNumber(Project $project,Sprint $sprint): int
    {
        $result = $this->createQueryBuilder('t')
            ->select('MAX(t.number) as maxNumber')
            ->andWhere('t.project = :project','t.sprint= :sprint')
            ->setParameter('project', $project)
            ->setParameter('sprint', $sprint)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    /**
     * @return Task[]
     */
    public function getDone(Project $project,Sprint $sprint): array
    {
        return $this->getByStatus($project,$sprint, Task::DONE);
    }

    /**
     * @return Task[]
     */
    public function getDoing(Project $project, Sprint $sprint): array
    {
        return $this->getByStatus($project,$sprint,Task::DOING);
    }

    /**
     * @return Task[]
     */
    public function getToDo(Project $project,Sprint $sprint): array
    {
        return $this->getByStatus($project,$sprint, Task::TODO);
    }

    public function getProportionStatus(Project $project,Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.status) as count, t.status as value')
            ->andWhere('t.project = :project','t.sprint= :sprint')
            ->setParameter('project', $project)
            ->setParameter('sprint', $sprint)
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
    }

    public function getProportionEstimationManDays(Project $project,Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(t.requiredManDays) as count, t.requiredManDays as value')
            ->andWhere('t.project = :project','t.sprint= :sprint')
            ->setParameters(array('project'=>$project,'sprint'=>$sprint))
            ->groupBy('t.requiredManDays')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMembersAssociated(Project $project,Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('Count(dev.name) as count, dev.name as value')
            ->andWhere('t.project = :project','t.sprint= :sprint')
            ->setParameters(array('project'=>$project,'sprint'=>$sprint))
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    public function getProportionMansDPerMembersAssociated(Project $project,Sprint $sprint): array
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(t.requiredManDays) as count, dev.name as value')
            ->andWhere('t.project = :project','t.sprint= :sprint')
            ->setParameters(array('project'=>$project,'sprint'=>$sprint))
            ->groupBy('t.developper')
            ->join('t.developper', 'dev')
            ->getQuery()
            ->getResult();
    }

    private function getByStatus(Project $project,Sprint $sprint, string $status): array
    {
        return $this->findBy([
            'sprint' => $sprint,
            'project' => $project,
            'status' => $status
        ]);
    }
}
