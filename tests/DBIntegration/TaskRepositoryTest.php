<?php


namespace App\Tests\DBIntegration;


use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    use RepositoryTestTrait;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->setUpTrait($kernel);

        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }


    public function testGetNextNumber(): void
    {
        $issues = $this->taskRepository->findAll();

        $maxNumberInProject1 = array_reduce($issues, function(int $number, Task $task) {
            return $task->getProject()->getId() == $this->project1->getId() ? max($number, $task->getNumber()) : $number;
        }, 0);
        $maxNumberInProject2 = array_reduce($issues, function(int $number, Task $task) {
            return $task->getProject()->getId() == $this->project2->getId() ? max($number, $task->getNumber()) : $number;
        }, 0);

        $this->assertEquals($maxNumberInProject1 + 1, $this->taskRepository->getNextNumber($this->project1));
        $this->assertEquals($maxNumberInProject2 + 1, $this->taskRepository->getNextNumber($this->project2));
    }

    public function testGetDone(): void
    {
        foreach ($this->taskRepository->getDone($this->project1) as $task) {
            self::assertEquals(Task::DONE, $task->getStatus());
        }
    }

    public function testGetDoing(): void
    {
        foreach ($this->taskRepository->getDoing($this->project1) as $task) {
            self::assertEquals(Task::DOING, $task->getStatus());
        }
    }

    public function testGetToDo(): void
    {
        foreach ($this->taskRepository->getToDo($this->project1) as $task) {
            self::assertEquals(Task::TODO, $task->getStatus());
        }
    }

    protected function tearDown(): void
    {
        $this->tearDownTrait();
    }
}
