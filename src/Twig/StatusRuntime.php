<?php


namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class StatusRuntime implements RuntimeExtensionInterface
{

    /**
     * @return string
     */
    public function getClassForStatus($status): string
    {
        $statusClass = '';
        switch($status){
            case 'todo':
                $statusClass = 'red';
                break;
            case 'doing':
                $statusClass = 'yellow';
                break;
            case 'done':
                $statusClass = 'green';
                break;
            default :
                $statusClass = '';
                break;
        }
        return $statusClass;
    }
}
