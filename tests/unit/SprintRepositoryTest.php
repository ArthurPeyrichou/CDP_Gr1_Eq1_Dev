<?php


namespace App\Tests\unit;


use App\Entity\Sprint;
use App\Repository\SprintRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SprintRepositoryTest extends KernelTestCase
{
    /**
     * @var SprintRepository
     */
    private $sprintRepository;

    use RepositoryTestTrait;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->setUpTrait($kernel);

        $this->sprintRepository = $this->entityManager->getRepository(Sprint::class);
    }


    public function testGetNextNumber(): void
    {
        $issues = $this->sprintRepository->findAll();

        $maxNumberInProject1 = array_reduce($issues, function(int $number, Sprint $sprint) {
            return $sprint->getProject()->getId() == $this->project1->getId() ? max($number, $sprint->getNumber()) : $number;
        }, 0);
        $maxNumberInProject2 = array_reduce($issues, function(int $number, Sprint $sprint) {
            return $sprint->getProject()->getId() == $this->project2->getId() ? max($number, $sprint->getNumber()) : $number;
        }, 0);

        $this->assertEquals($maxNumberInProject1 + 1, $this->sprintRepository->getNextNumber($this->project1));
        $this->assertEquals($maxNumberInProject2 + 1, $this->sprintRepository->getNextNumber($this->project2));
    }

    protected function tearDown(): void
    {
        $this->tearDownTrait();
    }
}
