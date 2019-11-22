<?php


namespace App\Tests\DBIntegration;


use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * A trait allowing to access the two test projects and exposing a method to close the entity manager
 */
trait RepositoryTestTrait
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Project
     */
    private $project1;

    /**
     * @var Project
     */
    private $project2;

    protected function setUpTrait(KernelInterface $kernel): void
    {
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->project1 = $this->entityManager->getRepository(Project::class)->findAll()[0];
        $this->project2 = $this->entityManager->getRepository(Project::class)->findAll()[1];
    }

    protected function tearDownTrait(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }

}
