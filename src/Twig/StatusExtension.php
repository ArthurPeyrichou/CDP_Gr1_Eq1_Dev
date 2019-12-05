<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getClassForStatus', [StatusRuntime::class, 'getClassForStatus']),
            new TwigFunction('getClassForTestStatus', [StatusRuntime::class, 'getClassForTestStatus'])
        ];
    }
}
