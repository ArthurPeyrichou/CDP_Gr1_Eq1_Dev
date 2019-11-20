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

    public function getDone(Project $project): array
    {
        return $this->getByStatus($project, Task::DONE);
    }

    public function getDoing(Project $project): array
    {
        return $this->getByStatus($project, Task::DOING);
    }

    public function getToDo(Project $project): array
    {
        return $this->getByStatus($project, Task::TODO);
    }

    private function getByStatus(Project $project, string $status): array
    {
        return $this->findBy([
            'project' => $project,
            'status' => $status
        ]);
    }
}
