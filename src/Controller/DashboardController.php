<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InvitationRepository;

class DashboardController extends AbstractController {
    
    /**
     * @Route("/dashboard", name = "dashboard", methods = {"GET"})
     * @Route("/", name = "root", methods = {"GET"})
     */
    public function viewDashboard(Request $request, InvitationRepository $invitationRepository) {
        /**@var $member Member */
        $member = $this->getUser();
        $myProjects = $member->getOwnedProjects();
        $myLinkedProjects = $member->getContributedProjects();

        $myInvitations = $invitationRepository->findBy([
            'member' => $member
        ]);

        return $this->render('project/dashboard.html.twig', ["myProjects"=> $myProjects,
                                                            "myLinkedProjects"=> $myLinkedProjects, 
                                                            "myInvitations"=> $myInvitations, 
                                                            'user' => $member]);
    }

}
