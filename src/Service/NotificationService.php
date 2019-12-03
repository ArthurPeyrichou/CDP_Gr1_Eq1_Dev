<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NotificationService
{
    public const ERROR = 'error';
    public const SUCCESS = 'success';
    public const INFO = 'info';

    /**
     * @var Session
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addSuccess(string $message): void
    {
        $this->addMessage($message, self::SUCCESS);
    }

    public function addError(string $message): void
    {
        $this->addMessage($message, self::ERROR);
    }

    public function addInfo(string $message): void
    {
        $this->addMessage($message, self::INFO);
    }

    private function addMessage(string $message, string $type): void
    {
        $this->session->getFlashBag()->add($type, $message);
    }

    /**
     * @return string[]
     */
    public function getMessages(string $type): array
    {
        if (!$this->session->getFlashBag()->has($type)) {
            return [];
        }
        return $this->session->getFlashBag()->get($type);
    }
}
