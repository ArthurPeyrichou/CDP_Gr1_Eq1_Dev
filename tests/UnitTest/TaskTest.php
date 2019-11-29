<?php


namespace App\Tests\UnitTest;


use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Task;
use App\EntityException\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

    private $project;

    /**
     * @dataProvider getTodoTasks
     */
    public function testCanBeginTodoTask(Task $task) {
        $task->begin();
        $this->assertEquals(Task::DOING, $task->getStatus());
    }

    /**
     * @dataProvider getDoingTasks
     * @dataProvider getDoneTasks
     */
    public function testCannotBeginDoingOrDoneTask(Task $task) {
        $this->expectException(InvalidStatusTransitionException::class);
        $task->begin();
    }

    /**
     * @dataProvider getTodoTasks
     * @dataProvider getDoingTasks
     */
    public function testCanFinishTodoOrDoingTask(Task $task) {
        $task->finish();
        $this->assertEquals(Task::DONE, $task->getStatus());
    }

    /**
     * @dataProvider getDoneTasks
     */
    public function testCanotFinishDoneTask(Task $task) {
        $this->expectException(InvalidStatusTransitionException::class);
        $task->finish();
    }

    public function getTodoTasks(): array
    {
        return [
            [new Task(1, 'abcd', 1, [], $this->getTestProject(), null)],
            [new Task(2, 'dcab', 0.2, [], $this->getTestProject(), null)],
            [new Task(3, 'test', 0, [], $this->getTestProject(), null)]
        ];
    }

    public function getDoingTasks(): array
    {
        return array_map(function (array $data) {
            $task = $data[0];
            $task->begin();
            return [$task];
        }, $this->getTodoTasks());
    }

    public function getDoneTasks(): array
    {
        return array_map(function (array $data) {
            $task = $data[0];
            $task->finish();
            return [$task];
        }, $this->getDoingTasks());
    }

    private function getTestProject(): Project
    {
        if (!$this->project) {
            $member = new Member('name', 'email@email.com', 'pass');
            $this->project = new Project($member, 'projName', 'projDesc', new \DateTimeImmutable());
        }
        return $this->project;
    }

}
