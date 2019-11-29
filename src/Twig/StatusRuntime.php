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
        switch($status){
            case "todo":
                return "red";
            case "doing":
                return "yellow";
            case "done":
                return "green";   
            default :
                return "";
        }

    }
}
