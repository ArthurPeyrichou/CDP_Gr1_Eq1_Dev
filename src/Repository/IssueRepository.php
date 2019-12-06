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
            ->where('i.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    public function getProportionStatus(Project $project): array
    {
        $todo = [];
        $doing = [];
        $done = [];

        $todo['count'] = 0;
        $todo['value'] = 'Todo';
        $doing['count'] = 0;
        $doing['value'] = 'Doing';
        $done['count'] = 0;
        $done['value'] = 'Done';

        foreach($this->findBy(['project' => $project]) as $issue){
            switch($issue->getStatus()){
                case Issue::TODO :
                    $todo['count']++;
                    break;
                case Issue::DOING :
                    $doing['count']++;
                    break;
                case Issue::DONE :
                    $done['count']++;
                    break;
                default:
                    break;
            }
        }

        return [$todo, $doing, $done];
    }

    public function getProportionPriority(Project $project): array
    {
        return $this->createQueryBuilder('i')
            ->select('Count(i.priority) as count, i.priority as value')
            ->where('i.project = :project')
            ->setParameter('project', $project)
            ->groupBy('i.priority')
            ->getQuery()
            ->getResult();
    }

    public function getProportionDifficulty(Project $project): array
    {
        return $this->createQueryBuilder('i')
            ->select('Count(i.difficulty) as count, i.difficulty as value')
            ->where('i.difficulty > 0')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project)
            ->groupBy('i.difficulty')
            ->getQuery()
            ->getResult();
    }

    public function getBurnDownStat(Project $project): array
    {
        $points = $this->createQueryBuilder('i')
            ->select('SUM(i.difficulty) as count, 0 as value')
            ->where('i.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

        $res = array();
        foreach($this->findBy(['project' => $project]) as $issue){

            if($issue->getStatus() == Issue::DONE
                && $issue->getSprints() != null) {
                $sprint =$issue->getSprints()->last();
                $line = $sprint->getNumber() - 1;
                if( empty($res[$line] ) ){
                    $res[$line]["value"] = $line + 1;
                    $res[$line]["count"] = $issue->getDifficulty();
                } else {
                    $res[$line]["count"]+= $issue->getDifficulty();
                }
                
            }
        }
        $res = new \ArrayObject($res);
        $res->asort();
        $cpt = 0;
        foreach($res as $point) {
            $cpt += $point['count'];
            $point['count'] = $points[0]['count'] - $cpt;
            $points[] = $point;
        }

        return $points;
    }

    public function getBurnDownTheoricStat(Project $project): array
    {
        $points = $this->createQueryBuilder('i')
            ->select('SUM(i.difficulty) as count, 0 as value')
            ->where('i.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

        $res = array();
        foreach($this->findBy(['project' => $project]) as $issue){

            if($issue->getSprints() != null) {
                $sprint =$issue->getSprints()->last();
                $line = $sprint->getNumber() - 1;
                if( empty($res[$line] ) ){
                    $res[$line]["value"] = $line + 1;
                    $res[$line]["count"] = $issue->getDifficulty();
                } else {
                    $res[$line]["count"]+= $issue->getDifficulty();
                }
            }
        }
        $res = new \ArrayObject($res);
        $res->asort();
        $cpt = 0;
        foreach($res as $point) {
            $cpt += $point['count'];
            $point['count'] = $points[0]['count'] - $cpt;
            $points[] = $point;
        }

        return $points;
    }
}
