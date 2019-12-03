<?php


namespace App\Tests\Integration\DB;


use App\Entity\Sprint;
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
        foreach ($this->getTestSprints() as $sprint) {
            self::assertEquals($this->computeNextTaskNumber($sprint), $this->taskRepository->getNextNumber($sprint));
        }
    }

    private function computeNextTaskNumber(Sprint $sprint) : int
    {
        return  array_reduce($sprint->getTasks()->getValues(), function(int $max, Task $task) {
                return max($max, $task->getNumber());
            }, 0) + 1;
    }

    public function testGetDone(): void
    {
        foreach ($this->getTestSprints() as $sprint) {
            foreach ($this->taskRepository->getDone($sprint) as $task) {
                self::assertEquals(Task::DONE, $task->getStatus());
            }
        }
    }

    public function testGetDoing(): void
    {
        foreach ($this->getTestSprints() as $sprint) {
            foreach ($this->taskRepository->getDoing($sprint) as $task) {
                self::assertEquals(Task::DOING, $task->getStatus());
            }
        }
    }

    public function testGetToDo(): void
    {
        foreach ($this->getTestSprints() as $sprint) {
            foreach ($this->taskRepository->getToDo($sprint) as $task) {
                self::assertEquals(Task::TODO, $task->getStatus());
            }
        }
    }

    private function getTestSprints(): array
    {
        return array_merge($this->project1->getSprints()->getValues(), $this->project2->getSprints()->getValues());
    }

    protected function tearDown(): void
    {
        $this->tearDownTrait();
    }
}
