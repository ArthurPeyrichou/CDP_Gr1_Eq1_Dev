<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getErrors', [NotificationRuntime::class, 'getErrors']),
            new TwigFunction('getSuccess', [NotificationRuntime::class, 'getSuccess'])
        ];
    }
}
