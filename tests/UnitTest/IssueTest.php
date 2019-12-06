<?php


namespace App\Tests\UnitTest;


use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Issue;
use App\Entity\Sprint;
use App\EntityException\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{

    private $testProject;

    private $testSprint;

    public function getTestProject(): Project
    {
        if (!$this->testProject) {
            $member = new Member('name', 'email@email.com', 'pass');
            $this->testProject = new Project($member, 'projName', 'projDesc', new \DateTimeImmutable());
        }
        return $this->testProject;
    }

    public function getTestIssue(): Issue
    {
        $sprints = array($this->getTestSprint());
        return new Issue(0, "Une desc", 10, Issue::PRIORITY_MEDIUM, $this->getTestProject(), $sprints);
    }

    public function getTestSprint(): Sprint
    {
        if (!$this->testSprint) {
            $this->testSprint = new Sprint($this->getTestProject(), 0, 'A test sprint', new \DateTimeImmutable('2019-01-01'), 14);
        }
        return $this->testSprint;
    }

    public function testgetStatus() {
        $sprint = $this->getTestSprint();
        $issue = $this->getTestIssue();

        $this->assertEquals(Issue::TODO, $issue->getStatus());
        $task1 = new Task(1, 'abcd', 1.0, array($issue), null, $sprint);
        $this->assertEquals(Issue::TODO, $issue->getStatus());

        $task1->begin();
        $this->assertEquals(Issue::DOING, $issue->getStatus());
        
        $task1->finish();
        $this->assertEquals(Issue::DONE, $issue->getStatus());
        
        $task2 = new Task(2, 'efgh', 2.0, array($issue), null, $sprint);
        $this->assertEquals(Issue::DOING, $issue->getStatus());
        
        $task2->begin();
        $this->assertEquals(Issue::DOING, $issue->getStatus());
        
        $task2->finish();
        $this->assertEquals(Issue::DONE, $issue->getStatus());
    }

    public function testgetProportionOfDone() {
        $sprint = $this->getTestSprint();
        $issue = $this->getTestIssue();

        $this->assertEquals("0%", $issue->getProportionOfDone());
        $task1 = new Task(1, 'abcd', 1.0, array($issue), null, $sprint);
        $this->assertEquals("0%", $issue->getProportionOfDone());

        $task1->begin();
        $this->assertEquals("0%", $issue->getProportionOfDone());
        
        $task1->finish();
        $this->assertEquals("100%", $issue->getProportionOfDone());
        
        $task2 = new Task(2, 'efgh', 2.0, array($issue), null, $sprint);
        $this->assertEquals("50%", $issue->getProportionOfDone());
        
        $task2->begin();
        $this->assertEquals("50%", $issue->getProportionOfDone());
        
        $task2->finish();
        $this->assertEquals("100%", $issue->getProportionOfDone());

        $task3 = new Task(2, 'efgh', 5.0, array($issue), null, $sprint);
        $this->assertEquals("66%", $issue->getProportionOfDone());
        
        $task3->begin();
        $this->assertEquals("66%", $issue->getProportionOfDone());
        
        $task3->finish();
        $this->assertEquals("100%", $issue->getProportionOfDone());
    }

}
