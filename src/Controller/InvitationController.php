<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Repository\MemberRepository;
use App\Repository\InvitationRepository;
use App\Service\Invitation\InvitationService;
use App\Service\RenderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends AbstractController
{

    /**
     * @Route("/project/{id}/sendInvitation", name="inviteToProject", methods={"POST"})
     */
    public function sendInvitationToProject(Request $request, InvitationService $invitationService,
            RenderService $renderService,MemberRepository $memberRepository, ProjectRepository $projectRepository,
            $id) : Response
    {

        $member = $memberRepository->findOneBy([
            'emailAddress' =>  $request->get('memberEmail')
        ]);

        $project = $projectRepository->findOneBy([
            'id' => $id
        ]);
        $owner = $project->getOwner();

        $success = null;
        $error = null;

        $user = $this->getUser();
        $status = null;

        if($owner == $user){
            $status = "owner";
            if($member && $project) {
                try {    
                    $invitationService->inviteUser($member, $project);
                    $success = 'Invitation envoyée avec succès';
                }  catch(\Exception $e) {
                    $error = $e->getMessage();
                }
            } else if($project) {
                $error = 'Ce membre n\'apparait pas dans nos registres...';
            } else if($member) {
                $error = 'Ce projet n\'apparait pas dans nos registres...';
            } else {
                $error = 'Ni le membre, ni le projet n\'apparaient dans nos registres...';
            }
        } else {
            $error =  'Vous ne pouvez pas inviter des membres dans ce projet';
        }

        return $this->render('project/project_details.html.twig',
        $renderService->renderProjectDetails($this->getUser(), $error, $success, $status, $project, null));
    }

    /**
     * @Route("/project/{invitationKey}/acceptInvitation", name="acceptInviteToProject", methods={"GET"})
     */
    public function acceptInvitationToProject(Request $request, InvitationRepository $invitationRepository,
            RenderService $renderService, $invitationKey) : Response
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

        return $this->render('project/dashboard.html.twig', 
        $renderService->renderDashboard($this->getUser(), $error, $success, $invitation));
    }

        /**
     * @Route("/project/{invitationKey}/denyInvitation", name="denyInviteToProject", methods={"GET"})
     */
    public function denyInvitationToProject(Request $request, InvitationRepository $invitationRepository, 
            RenderService $renderService, $invitationKey) : Response
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
        }  else {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($invitation);
                $entityManager->flush();
                $success = "Vous venez de refuser l'invitation de {$invitation->getProject()->getOwner()->getName()} à rejoindre sont projet";
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('project/dashboard.html.twig', 
        $renderService->renderDashboard($this->getUser(), $error, $success, $invitation));
    }

}
