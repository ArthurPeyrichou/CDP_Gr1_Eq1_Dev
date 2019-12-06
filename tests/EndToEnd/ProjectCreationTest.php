<?php


namespace App\Tests\EndToEnd;


use App\Entity\Project;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\PantherTestCase;

class ProjectCreationTest extends PantherTestCase
{
    private const PROJECT_NAME = 'Some random project';
    private const PROJECT_DESC = 'As the title says, this is some random project';
    private const EDITED_DESC = 'This is now some random edited project';

    use E2ETestTrait;

    private $client;

    public static function setUpBeforeClass(): void
    {
        $kernel = static::bootKernel();

        static::setUpBeforeClassTrait($kernel);
    }

    protected function setUp(): void
    {
        static::startWebServer();
        $this->client = static::createPantherClient();
        $this->logIn($this->client, 'member1@domain.com', 'someReallySecurePassword');
    }


    public function testAddProject(): void
    {
        $this->client->request('GET', '/dashboard');

        $this->waitForElement($this->client, '#navbarContent > div.navbar-nav.mr-auto > a:nth-child(2)')->click();

        $this->waitForElement($this->client, 'body > div > div > div > form');

        $this->typeInto($this->client, '#project_name', self::PROJECT_NAME);
        $this->typeInto($this->client, '#project_description', self::PROJECT_DESC);
        $this->clickOn($this->client, 'body > div > div > div > form > button');

        $this->assertNotNull($this->getOneFromDb(Project::class, [
            'name' => self::PROJECT_NAME,
            'description' => self::PROJECT_DESC
        ]));
    }

    public function testDisplayProjects(): void
    {
        $this->client->request('GET', '/dashboard');

        $projectElements = $this->waitForElement($this->client, '#project')
            ->findElements(WebDriverBy::cssSelector('#project > a'));

        $newProjectElement = $projectElements[count($projectElements) - 1];

        $this->assertStringContainsString(self::PROJECT_NAME, $newProjectElement->getText());
        $this->assertStringContainsString(self::PROJECT_DESC, $newProjectElement->getText());

        $newProjectElement->click();

        $projectTitle = $this->waitForElement($this->client, '#content > h1')->getText();
        $projectDescription = $this->client->findElement(
            WebDriverBy::cssSelector('#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > p.text-justify')
        )->getText();

        $this->assertEquals(self::PROJECT_NAME, $projectTitle);
        $this->assertStringContainsString(self::PROJECT_DESC, $projectDescription);
    }

    public function testEditProject(): void
    {
        $this->client->request('GET', '/dashboard');

        $projectElements = $this->waitForElement($this->client, '#project')
            ->findElements(WebDriverBy::cssSelector('#project > a'));
        $projectElements[count($projectElements) - 1]->click();

        $this->waitForElement($this->client,
            '#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > a'
        )->click();

        $this->waitForElement($this->client, '#project_description')->clear()->sendKeys(self::EDITED_DESC);
        $this->clickOn($this->client, '#content > div > div > div > form > button');

        $this->waitForElement($this->client, '#content > h1');

        $this->assertNotNull($this->getOneFromDb(Project::class, [
            'name' => self::PROJECT_NAME,
            'description' => self::EDITED_DESC
        ]));

    }

    public function testDeleteProject(): void
    {
        $this->client->request('GET', '/dashboard');

        $projectElements = $this->waitForElement($this->client, '#project')
            ->findElements(WebDriverBy::cssSelector('#project > a'));
        $projectElements[count($projectElements) - 1]->click();

        $this->waitForElement($this->client,
            '#content > div.container > div > div.col-sm-6.col-lg-8 > div > div > button'
        )->click();
        $this->client->wait()->until(
            WebDriverExpectedCondition::elementToBeClickable(
                WebDriverBy::cssSelector('#project-delete-confirm > div > div > div.modal-footer > a')
            )
        )->click();

        $this->waitForElement($this->client, '#project');

        $this->assertNull($this->getOneFromDb(Project::class, [
            'name' => self::PROJECT_NAME
        ]));
    }

    protected function tearDown(): void
    {
        $this->logOut($this->client);
        static::stopWebServer();
    }

    public static function tearDownAfterClass(): void
    {
        static::tearDownAfterClassTrait();
    }
}
