<?php


namespace App\Tests\EndToEnd;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

trait E2ETestTrait
{

    /**
     * @internal
     */
    private static $deletables = [];

    /**
     * @var EntityManagerInterface
     */
    private static $entityManager;

    /**
     * Performs common setup tasks for E2E tests. It needs to be called in the setUpBeforeClass method.
     * @param KernelInterface $kernel The app's kernel.
     */
    private static function setUpBeforeClassTrait(KernelInterface $kernel): void
    {
        self::$entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Retrieves an entity from the database.
     * @param string $className The name of the entity's class.
     * @param array $criteria An array of criteria to use to perform the query.
     * @return object|null The entity if one is found, null otherwise.
     */
    private function getOneFromDb(string $className, array $criteria): ?object
    {
        return self::$entityManager->getRepository($className)->findOneBy($criteria);
    }

    /**
     * Retrieves an entity from the database and mark it as to be deleted in the teardown phase.
     * @param string $className The name of the entity's class.
     * @param array $criteria An array of criteria to use to perform the query.
     * @return object|null The entity if one is found, null otherwise.
     */
    private function getAndMarkAsDeletable(string $className, array $criteria): ?object
    {
        $entity = $this->getOneFromDb($className, $criteria);
        if ($entity) {
            self::$deletables[] = [
                'id' => $entity->getId(),
                'className' => $className
            ];
        }
        return $entity;
    }

    /**
     * Performs common teardown tasks for E2E tests. This remove all entities marked as deletable from the database.
     * This method has to be called in the tearDownAfterClass method.
     */
    private static function tearDownAfterClassTrait(): void
    {
        foreach (self::$deletables as $deletable) {
            $entity = self::$entityManager->find($deletable['className'], $deletable['id']);
            self::$entityManager->remove($entity);
        }
        self::$entityManager->flush();
    }

}
