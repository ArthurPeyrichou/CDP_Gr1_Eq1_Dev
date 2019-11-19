<?php


namespace App\Twig;


use App\Service\NotificationService;
use Twig\Extension\RuntimeExtensionInterface;

class NotificationRuntime implements RuntimeExtensionInterface
{
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->notificationService->getMessages(NotificationService::ERROR);
    }

    /**
     * @return string[]
     */
    public function getSuccess(): array
    {
        return $this->notificationService->getMessages(NotificationService::SUCCESS);
    }

}
