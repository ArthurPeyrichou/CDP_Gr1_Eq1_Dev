<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {
    
    /**
     * @Route("/dashboard", name = "dashboard", methods = {"GET"})
     */
    public function viewDashboard(Request $request) {
        $member = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $myProjects = $repository->findBy([
            'MANAGER_ID' => $member->getId()
        ]);
        //Remplacer la requette pour selectionner les projet lié et non les projet enfants
        $myLinkedProjects = $repository->findBy([
            'MANAGER_ID' => $member->getId()
        ]);
        $pseudo = $member->getName();

        return $this->render('project/dashboard.html.twig', ["myProjects"=> $myProjects,
                                                            "myLinkedProjects"=> $myLinkedProjects, 
                                                            "pseudo"=> $pseudo]);
    }

}
