<?php


namespace App\Tests\EndToEnd;


use App\Entity\Member;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\PantherTestCase;

class UserManagementTest extends PantherTestCase
{
    private const LOGIN = 'A random user';
    private const EMAIL = 'randomuser@random.com';
    private const PW = 'randomPass';

    use E2ETestTrait;

    public static function setUpBeforeClass(): void
    {
        $kernel = static::bootKernel();

        static::setUpBeforeClassTrait($kernel);
    }

    protected function setUp(): void
    {
        static::startWebServer();
    }

    public function testRegister(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/register');

        $this->waitForElement($client, 'body > div > div > div > form');

        $this->typeInto($client, '#registration_name', self::LOGIN);
        $this->typeInto($client, '#registration_emailAddress', self::EMAIL);
        $this->typeInto($client, '#registration_password_first', self::PW);
        $this->typeInto($client, '#registration_password_second', self::PW);
        $this->clickOn($client, 'body > div > div > div > form > button');

        $client->wait()->until(WebDriverExpectedCondition::urlContains('/login'));

        $this->assertNotNull($this->getAndMarkAsDeletable(Member::class, [
            'emailAddress' => self::EMAIL,
            'name' => self::LOGIN
        ]));
    }

    public function testConnect(): void
    {

        $client = static::createPantherClient();
        $this->logIn($client, self::EMAIL, self::PW);

        $client->wait()->until(WebDriverExpectedCondition::urlContains('/dashboard'));
        $loginTitle = $client->findElement(WebDriverBy::cssSelector('#navbarContent > div.dropdown.px-lg-3 > a'));

        $this->assertStringContainsString(self::LOGIN, $loginTitle->getText());
    }

    protected function tearDown(): void
    {
        static::stopWebServer();
    }

    public static function tearDownAfterClass(): void
    {
        static::tearDownAfterClassTrait();
    }

}
