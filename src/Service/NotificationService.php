<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * A service to post messages that will be displayed needs to be displayed to the user at a later time.
 */
class NotificationService
{
    /**
     * A constant used to tag a message as an error.
     */
    public const ERROR = 'error';
    /**
     * A constant used to tag a message as a success.
     */
    public const SUCCESS = 'success';
    /**
     * A constant used to tag a message as a piece of information.
     */
    public const INFO = 'info';

    /**
     * @var Session
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Posts a new success message in the list.
     * @param string $message The message to post.
     */
    public function addSuccess(string $message): void
    {
        $this->addMessage($message, self::SUCCESS);
    }

    /**
     * Posts a new error message in the list.
     * @param string $message The message to post.
     */
    public function addError(string $message): void
    {
        $this->addMessage($message, self::ERROR);
    }

    /**
     * Posts a new informative message in the list.
     * @param string $message The message to post.
     */
    public function addInfo(string $message): void
    {
        $this->addMessage($message, self::INFO);
    }

    private function addMessage(string $message, string $type): void
    {
        $this->session->getFlashBag()->add($type, $message);
    }

    /**
     * Get all messages of a specific type that have been posted using the service.
     * @param $type string The type of messages to return.
     * @return string[] The posted messages corresponding to the provided category.
     */
    public function getMessages(string $type): array
    {
        if (!$this->session->getFlashBag()->has($type)) {
            return [];
        }
        return $this->session->getFlashBag()->get($type);
    }
}
