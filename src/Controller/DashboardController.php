<?php
// src/Controller/DashboardController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\PlanningPoker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InvitationRepository;
use App\Repository\PlanningPokerRepository;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractController {
    
    /**
     * @Route("/dashboard", name = "dashboard", methods = {"GET"})
     * @Route("/", name = "root", methods = {"GET"})
     */
    public function viewDashboard(Request $request, InvitationRepository $invitationRepository,
                PlanningPokerRepository $planningPokerRepository, EntityManagerInterface $entityManager, 
                NotificationService $notifications) {
        /**@var $member Member */
        $member = $this->getUser();
        $myProjects = $member->getOwnedProjects();
        $myLinkedProjects = $member->getContributedProjects();
        $planningPokers = $planningPokerRepository->getPlanningPokerNotDoneByMember($member);
        $today = new \DateTime();
        foreach($planningPokers as $planningPoker){
            if(date_diff($planningPoker->getCreationDate(),$today)->format('%d') > PlanningPoker::TIME) {
                $issue = $planningPoker->getIssue();
                
                $entityManager->remove($planningPoker);
                $entityManager->flush();

                if($planningPokerRepository->isPlanningPokerDoneByIssue($issue) ) {
                    $cpt = 0;
                    $amount = 0;
                    foreach($planningPokerRepository->getPlanningPokerByIssue($issue) as $planningPokerDone) {
                        ++$cpt;
                        $amount += $planningPokerDone->getValue();
                        $entityManager->remove($planningPokerDone);
                        $entityManager->flush();
                    }
                    $issue->setDifficulty($amount / $cpt);
                    $entityManager->persist($issue);
                    $entityManager->flush();
                    $notifications->addSuccess("Fin du planning poker pour l'issue {$issue->getNumber()}.");
                }
                
                $notifications->addError("Vous avez dépassé le temps permis pour participer au planning poker de l'issue {$issue->getNumber()}.");
            }
        }

        $myInvitations = $invitationRepository->findBy([
            'member' => $member
        ]);

        $planningPokers = $planningPokerRepository->getPlanningPokerNotDoneByMember($member);

        return $this->render('project/dashboard.html.twig', ["myProjects"=> $myProjects,
                                                            "myLinkedProjects"=> $myLinkedProjects, 
                                                            "myInvitations"=> $myInvitations, 
                                                            "myPlanningPokers"=> $planningPokers, 
                                                            'user' => $member]);
    }

}
