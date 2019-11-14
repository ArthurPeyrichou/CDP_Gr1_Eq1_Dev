<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Repository\MemberRepository;
use App\Repository\InvitationRepository;
use App\Service\Invitation\InvitationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends AbstractController
{

    /**
     * @Route("/project/{id}/sendInvitation", name="inviteToProject", methods={"POST"})
     */
    public function sendInvitationToProject(Request $request, InvitationService $invitationService,
                                            MemberRepository $memberRepository, ProjectRepository $projectRepository,
                                            $id) : Response
    {

        $theMember = $memberRepository->findOneBy([
            'emailAddress' =>  $request->get('memberEmail')
        ]);

        $myProject = $projectRepository->findOneBy([
            'id' => $id
        ]);

        $success = null;
        $error = null;

        if($theMember) {
            try {
                $invitationService->inviteUser($theMember, $myProject);
                $success = 'Invitation envoyée avec succès';
            }
            catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        else {
            $error = 'Ce membre n\'apparait pas dans nos registres...';
        }

        $owner = $myProject->getOwner();
        return $this->render('project/project_details.html.twig', [
            'success' => $success,
            'error' => $error,
            'project' => $myProject,
            'owner' => $owner,
            'members' => $myProject->getMembers(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{invitationKey}/acceptInvitation", name="acceptInviteToProject", methods={"GET"})
     */
    public function acceptInvitationToProject(Request $request, InvitationRepository $invitationRepository,
                                              $invitationKey) : Response
    {

        $member = $this->getUser();
        $success = null;
        $error = null;

        $invitation = $invitationRepository->findOneBy([
            'invitationKey' => $invitationKey,
            'member' => $member
        ]);
        if($invitation == null) {
            $error = 'L\'invitation ne vous est pas adressée ou n\'existe pas';
        }
        else {
            try {
                $project = $invitation->getProject();
                $member->addContributedProject($project);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($member);
                $entityManager->remove($invitation);
                $entityManager->flush();
                $success = "Vous venez d'accepter l'invitation de {$project->getOwner()->getName()} à rejoindre sont projet";
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $myProjects = $member->getOwnedProjects();
        $myLinkedProjects = $member->getContributedProjects();
        $myInvitations = $invitationRepository->findBy([
            'member' => $member
        ]);

        return $this->render('project/dashboard.html.twig', [
            "success"=> $success,
            "error"=> $error,
            "myProjects"=> $myProjects,
            "myLinkedProjects"=> $myLinkedProjects,
            "myInvitations"=> $myInvitations,
            'user' => $this->getUser()
        ]);
    }

}
