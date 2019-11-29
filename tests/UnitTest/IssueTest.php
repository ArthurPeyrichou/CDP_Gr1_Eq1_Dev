<?php


namespace App\Tests\UnitTest;


use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Issue;
use App\EntityException\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{

    private $project;

    private function getTestProject(): Project
    {
        if (!$this->project) {
            $member = new Member('name', 'email@email.com', 'pass');
            $this->project = new Project($member, 'projName', 'projDesc', new \DateTimeImmutable());
        }
        return $this->project;
    }

    private function getTestIssue(): Issue
    {
        if (!$this->project) {
            $issue = new Issue(0, $this->description, $difficulty, $priority, $status, $project, $sprint = null);
            $this->project = new Project($member, 'projName', 'projDesc', new \DateTimeImmutable());
        }
        return $this->project;
    }

}
