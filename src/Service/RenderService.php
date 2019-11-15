<?php


namespace App\Service;


use App\Entity\Invitation;
use App\Entity\Member;
use App\Entity\Project;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;

class RenderService
{

    private $invitationRepository;

    private $entityManager;

    public function __construct(InvitationRepository $invitationRepository, EntityManagerInterface $entityManager)
    {
        $this->invitationRepository = $invitationRepository;
        $this->entityManager = $entityManager;
    }

    public function renderProjectDetails($user, $error, $success, $status, $project, $invitation) {

        return [
            'error' => $error,
            'success' => $success,
            'status' => $status,
            'myInvitation' => $invitation,
            'project' => $project,
            'owner' => $project->getOwner(),
            'members' => $project->getMembers(),
            'user' => $user
        ];
    }

    public function renderDashboard($user, $error, $success) {

        $myProjects = $user->getOwnedProjects();
        $myLinkedProjects = $user->getContributedProjects();
        $myInvitations = $this->invitationRepository->findBy([
            'member' => $user
        ]);

        return [
            "success"=> $success,
            "error"=> $error,
            "myProjects"=> $myProjects,
            "myLinkedProjects"=> $myLinkedProjects,
            "myInvitations"=> $myInvitations,
            'user' => $user
        ];
    }


}
