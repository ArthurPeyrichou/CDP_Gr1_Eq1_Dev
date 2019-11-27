<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InvitationRepository;
use App\Repository\PlanningPokerRepository;

class DashboardController extends AbstractController {
    
    /**
     * @Route("/dashboard", name = "dashboard", methods = {"GET"})
     * @Route("/", name = "root", methods = {"GET"})
     */
    public function viewDashboard(Request $request, InvitationRepository $invitationRepository, PlanningPokerRepository $planningPokerRepository) {
        /**@var $member Member */
        $member = $this->getUser();
        $myProjects = $member->getOwnedProjects();
        $myLinkedProjects = $member->getContributedProjects();

        $myInvitations = $invitationRepository->findBy([
            'member' => $member
        ]);
        $myPlanningPokers = $planningPokerRepository->findBy([
            'member' => $member
        ]);

        return $this->render('project/dashboard.html.twig', ["myProjects"=> $myProjects,
                                                            "myLinkedProjects"=> $myLinkedProjects, 
                                                            "myInvitations"=> $myInvitations, 
                                                            "myPlanningPokers"=> $planningPokerRepository->getPlanningPokerNotDoneByMember($member), 
                                                            'user' => $member]);
    }

}
