<?php


namespace App\Service;


class NotificationService
{
    public const ERROR = 'error';
    public const SUCCESS = 'success';

    private $messages = [
        self::ERROR => [],
        self::SUCCESS => []
    ];

    public function addSuccess(string $message): void
    {
        $this->addMessage($message, self::SUCCESS);
    }

    public function addError(string $message): void
    {
        $this->addMessage($message, self::ERROR);
    }

    private function addMessage(string $message, string $type): void
    {
        $this->messages[$type][] = $message;
    }

    /**
     * @return string[]
     */
    public function getMessages(string $type): array
    {
        if (!array_key_exists($type, $this->messages)) {
            return [];
        }
        return $this->messages[$type];
    }
}
