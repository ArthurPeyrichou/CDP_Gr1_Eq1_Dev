<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Repository\MemberRepository;
use App\Repository\InvitationRepository;
use App\Service\Invitation\InvitationService;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends AbstractController
{

    private $notifications;
    private $entityManager;

    public function __construct(NotificationService $notifications, EntityManagerInterface $entityManager)
    {
        $this->notifications = $notifications;
        $this->entityManager = $entityManager;
    }

    /**
     * Handles the action of sending an invitation to a user.
     * @Route("/project/{id}/sendInvitation", name="inviteToProject", methods={"POST"})
     */
    public function sendInvitationToProject(Request $request, InvitationService $invitationService,
                                            MemberRepository $memberRepository, ProjectRepository $projectRepository,
                                            $id) : Response
    {
        $member = $memberRepository->findOneBy([
            'emailAddress' =>  $request->get('memberEmail')
        ]);

        $project = $projectRepository->find($id);

        if($project->getOwner() == $this->getUser()){
            if($member && $project) {
                try {
                    $invitationService->inviteUser($member, $project);
                    $this->notifications->addSuccess('Invitation envoyée avec succès');
                }  catch(\Exception $e) {
                    $this->notifications->addError($e->getMessage());
                }
            } else if($project) {
                $this->notifications->addError('Ce membre n\'apparait pas dans nos registres...');
            } else if($member) {
                $this->notifications->addError('Ce projet n\'apparait pas dans nos registres...');
            } else {
                $this->notifications->addError('Ni le membre ni le projet n\'apparaissent dans nos registres...');
            }
        } else {
            $this->notifications->addError('Vous ne pouvez pas inviter des membres dans ce projet');
        }

        return $this->redirectToRoute('projectDetails', [
            'id' => $id
        ]);
    }

    /**
     * Handles the action to accept an invitation.
     * @Route("/project/{invitationKey}/acceptInvitation", name="acceptInviteToProject", methods={"GET"})
     */
    public function acceptInvitationToProject(Request $request, InvitationRepository $invitationRepository, $invitationKey) : Response
    {
        $member = $this->getUser();

        $invitation = $invitationRepository->findOneBy([
            'invitationKey' => $invitationKey,
            'member' => $member
        ]);
        if($invitation == null) {
            $this->notifications->addError('L\'invitation ne vous est pas adressée ou n\'existe pas');
        }
        else {
            try {
                $project = $invitation->getProject();
                $member->addContributedProject($project);
                $this->entityManager = $this->getDoctrine()->getManager();
                $this->entityManager->persist($member);
                $this->entityManager->remove($invitation);

                $notif = new Notification("Bonne nouvelle! {$member->getName()} a accepté votre invitation.");
                $project->getOwner()->addNotification($notif);
                $this->entityManager->persist($notif);

                foreach($project->getMembers() as $member) {
                    if($this->getUser()->getId() != $member->getId()){
                        $notif = new Notification("{$member->getName()} à rejoins le projet {$project->getName()}.");
                        $project->getOwner()->addNotification($notif);
                        $this->entityManager->persist($notif);
                    }
                }

                $this->entityManager->flush();
                $this->notifications->addSuccess("Vous venez d'accepter l'invitation de {$project->getOwner()->getName()} à rejoindre son projet");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * Handles the action of denying an invitation.
     * @Route("/project/{invitationKey}/denyInvitation", name="denyInviteToProject", methods={"GET"})
     */
    public function denyInvitationToProject(Request $request, InvitationRepository $invitationRepository, $invitationKey) : Response
    {

        $member = $this->getUser();

        $invitation = $invitationRepository->findOneBy([
            'invitationKey' => $invitationKey,
            'member' => $member
        ]);
        if($invitation == null) {
            $this->notifications->addError('L\'invitation ne vous est pas adressée ou n\'existe pas');
        }  else {
            try {
                $this->entityManager->remove($invitation);

                $project = $invitation->getProject();
                $notif = new Notification("Aie.. {$member->getName()} a refusé votre invitation.");
                $project->getOwner()->addNotification($notif);
                $this->entityManager->persist($notif);

                $this->entityManager->flush();
                $this->notifications->addSuccess("Vous venez de refuser l'invitation de {$invitation->getProject()->getOwner()->getName()} à rejoindre son projet");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->redirectToRoute('dashboard');
    }

}
