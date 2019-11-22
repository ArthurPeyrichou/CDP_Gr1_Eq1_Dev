<?php


namespace App\Tests\unit;


use App\Entity\Issue;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class IssueRepositoryTest extends KernelTestCase
{
    /**
     * @var IssueRepository
     */
    private $issueRepository;

    use RepositoryTestTrait;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->setUpTrait($kernel);

        $this->issueRepository = $this->entityManager->getRepository(Issue::class);
    }


    public function testGetNextNumber(): void
    {
        $issues = $this->issueRepository->findAll();

        $maxNumberInProject1 = array_reduce($issues, function(int $number, Issue $issue) {
            return $issue->getProject()->getId() == $this->project1->getId() ? max($number, $issue->getNumber()) : $number;
        }, 0);
        $maxNumberInProject2 = array_reduce($issues, function(int $number, Issue $issue) {
            return $issue->getProject()->getId() == $this->project2->getId() ? max($number, $issue->getNumber()) : $number;
        }, 0);

        $this->assertEquals($maxNumberInProject1 + 1, $this->issueRepository->getNextNumber($this->project1));
        $this->assertEquals($maxNumberInProject2 + 1, $this->issueRepository->getNextNumber($this->project2));
    }

    protected function tearDown(): void
    {
        $this->tearDownTrait();
    }
}
