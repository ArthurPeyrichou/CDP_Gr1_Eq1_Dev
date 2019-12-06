<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Sprint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sprint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sprint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sprint[]    findAll()
 * @method Sprint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sprint::class);
    }

    public function getNextNumber(Project $project): int
    {
        $result = $this->createQueryBuilder('s')
            ->select('MAX(s.number) as maxNumber')
            ->andWhere('s.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult()[0]['maxNumber'];

        return $result + 1;
    }

    public function getBurnDownStat(Project $project): array
    {
        $points = $this->createQueryBuilder('s')
            ->select('s.theoricDoneDiff as count, s.number as value')
            ->where('s.project = :project')
            ->andWhere('s.doneDiff >= 0')
            ->orderBy('s.startDate')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

        $cpt = 0;
        foreach($points as $point) {
            $cpt += $point['count'];
        }
        
        $points = $this->createQueryBuilder('s')
            ->select('s.doneDiff as count, s.number as value')
            ->where('s.project = :project')
            ->andWhere('s.doneDiff >= 0')
            ->orderBy('s.startDate')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

        $res = array(["count" => $cpt, "value" => "0"]);
        $desc = 0;
        foreach($points as $point) {
            $desc  += $point['count'];
            $point['count'] = $cpt - $desc;
            $res []= ["count" =>$point['count'], "value" => $point['value']];
        }
        
        return $res;
    }

    public function getBurnDownTheoricStat(Project $project): array
    {
        $points = $this->createQueryBuilder('s')
            ->select('s.theoricDoneDiff as count, s.number as value')
            ->where('s.project = :project')
            ->andWhere('s.doneDiff >= 0')
            ->orderBy('s.startDate')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();

        $cpt = 0;
        foreach($points as $point) {
            $cpt += $point['count'];
        }
        $res = array(["count" => $cpt, "value" => "0"]);
        $desc = 0;
        foreach($points as $point) {
            $desc  += $point['count'];
            $point['count'] = $cpt - $desc;
            $res []= ["count" =>$point['count'], "value" => $point['value']];
        }
        
        return $res;
    }
}
