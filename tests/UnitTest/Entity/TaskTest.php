<?php


namespace App\Tests\UnitTest\Entity;


use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Sprint;
use App\Entity\Task;
use App\EntityException\InvalidStatusTransitionException;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
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
            [new Task(1, 'abcd', 1, [], null, $this->getMockSprint())],
            [new Task(2, 'dcab', 0.2, [], null, $this->getMockSprint())],
            [new Task(3, 'test', 0, [], null, $this->getMockSprint())]
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

    private function getMockSprint(): Sprint
    {
        return $this->createStub(Sprint::class);
    }

}
