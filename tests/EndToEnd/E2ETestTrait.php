<?php


namespace App\Tests\EndToEnd;


use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Panther\Client;

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

    /**
     * Performs the actions a user needs to perform to log in into the application.
     * @param Client $client An initialized web client.
     * @param string $email The email address to use.
     * @param string $password The password to use.
     */
    private function logIn(Client $client, string $email, string $password)
    {
        $client->request('GET', '/login');

        $client->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('body > div > div > div > form')
            )
        );
        $loginField = $client->findElement(WebDriverBy::id('email'));
        $loginField->sendKeys($email);
        $emailField = $client->findElement(WebDriverBy::id('password'));
        $emailField->sendKeys($password);
        $button = $client->findElement(WebDriverBy::cssSelector('body > div > div > div > form > button'));
        $button->click();
    }

    /**
     * Provides a more concise way to wait for an element to be present in the browser.
     * @param Client $client An initialized web client.
     * @param string $cssSelector The CSS selector to wait for.
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException If the element is not found.
     * @throws \Facebook\WebDriver\Exception\TimeOutException If the 30 seconds timeout is reached.
     */
    private function waitForElement(Client $client, string $cssSelector)
    {
        $client->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector($cssSelector))
        );
    }

    /**
     * Provides a more concise way to type text into an input.
     * @param Client $client An initialized web client.
     * @param string $cssSelector The CSS selector of the form input.
     * @param string $toType The text to type into the element.
     */
    private function typeInto(Client $client, string $cssSelector, string $toType)
    {
        $client->findElement(WebDriverBy::cssSelector($cssSelector))->sendKeys($toType);
    }

    /**
     * Provides a more concise way to click on an element.
     * @param Client $client An initialized web client.
     * @param string $cssSelector The CSS selector of the element to click on.
     */
    private function clickOn(Client $client, string $cssSelector)
    {
        $client->findElement(WebDriverBy::cssSelector($cssSelector))->click();
    }

}
