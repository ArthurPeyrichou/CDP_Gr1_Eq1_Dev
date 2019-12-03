<?php


namespace App\Tests\EndToEnd;


use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\PantherTestCase;

class UserManagementTest extends PantherTestCase
{
    private const LOGIN = 'A random user';
    private const EMAIL = 'randomuser@random.com';
    private const PASSWD = 'randomPass';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testRegister(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/register');

        $client->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('body > div > div > div > form'))
        );
        $loginField = $client->findElement(WebDriverBy::id('registration_name'));
        $loginField->sendKeys(self::LOGIN);
        $emailField = $client->findElement(WebDriverBy::id('registration_emailAddress'));
        $emailField->sendKeys(self::EMAIL);
        $passField = $client->findElement(WebDriverBy::id('registration_password_first'));
        $passField->sendKeys(self::PASSWD);
        $passFieldSec = $client->findElement(WebDriverBy::id('registration_password_second'));
        $passFieldSec->sendKeys(self::PASSWD);
        $button = $client->findElement(WebDriverBy::cssSelector('body > div > div > div > form > button'));
        $button->click();

        $client->wait()->until(WebDriverExpectedCondition::urlContains('/login'));

        $this->assertNotNull($this->entityManager->getRepository(Member::class)->findOneBy([
            'emailAddress' => self::EMAIL,
            'name' => self::LOGIN
        ]));
    }

    public function testConnect(): void
    {

        $client = static::createPantherClient();
        $client->request('GET', '/login');

        $client->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('body > div > div > div > form')
            )
        );
        $loginField = $client->findElement(WebDriverBy::id('email'));
        $loginField->sendKeys(self::EMAIL);
        $emailField = $client->findElement(WebDriverBy::id('password'));
        $emailField->sendKeys(self::PASSWD);
        $button = $client->findElement(WebDriverBy::cssSelector('body > div > div > div > form > button'));
        $button->click();

        $client->wait()->until(WebDriverExpectedCondition::urlContains('/dashboard'));

        $loginTitle = $client->findElement(WebDriverBy::cssSelector('#navbarContent > div.dropdown.px-lg-3 > a'));

        $this->assertContains(self::LOGIN, $loginTitle->getText());
    }

}
