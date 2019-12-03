<?php


namespace App\DataFixtures;


use App\Entity\Issue;
use App\Entity\Project;
use App\Entity\Sprint;

trait FixtureTrait
{
    private function getIssue(Project $project, int $number): Issue
    {
        return $project->getIssues()[$number - 1];
    }

    private function getSprint(Project $project, int $number): Sprint
    {
        return $project->getSprints()[$number - 1];
    }

}
