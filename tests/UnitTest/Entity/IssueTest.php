<?php


namespace App\Tests\UnitTest\Entity;


use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Issue;
use App\Entity\Sprint;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{
    private function getMockProject(): Project
    {
        return $this->createStub(Project::class);
    }

    private function getMockSprint(): Sprint
    {
        return $this->createStub(Sprint::class);
    }

    private function getTestIssue(): Issue
    {
        return new Issue(0, "Une desc", 10, Issue::PRIORITY_MEDIUM, $this->getMockProject(),
            $this->getMockSprint());
    }

    public function testgetStatus() {
        $sprint = $this->getMockSprint();
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
        $sprint = $this->getMockSprint();
        $issue = $this->getTestIssue();

        $this->assertEquals(0, $issue->getProportionOfDone());
        $task1 = new Task(1, 'abcd', 1.0, array($issue), null, $sprint);
        $this->assertEquals(0, $issue->getProportionOfDone());

        $task1->begin();
        $this->assertEquals(0, $issue->getProportionOfDone());

        $task1->finish();
        $this->assertEquals(1, $issue->getProportionOfDone());

        $task2 = new Task(2, 'efgh', 2.0, array($issue), null, $sprint);
        $this->assertEquals(0.5, $issue->getProportionOfDone());

        $task2->begin();
        $this->assertEquals(0.5, $issue->getProportionOfDone());

        $task2->finish();
        $this->assertEquals(1, $issue->getProportionOfDone());

        $task3 = new Task(2, 'efgh', 5.0, array($issue), null, $sprint);
        $this->assertEquals(0.67, $issue->getProportionOfDone());

        $task3->begin();
        $this->assertEquals(0.67, $issue->getProportionOfDone());

        $task3->finish();
        $this->assertEquals(1.0, $issue->getProportionOfDone());
    }

}
