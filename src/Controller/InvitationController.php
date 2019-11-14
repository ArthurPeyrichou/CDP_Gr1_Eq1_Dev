<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use App\Repository\MemberRepository;
use App\Service\Invitation\InvitationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends AbstractController
{

     /**
     * @Route("/project/{id}/sendInvitation", name="inviteToProject", methods={"POST"})
     */
    public function sendInvitationToProject(Request $request, InvitationService $invitationService, 
                MemberRepository $memberRepository, ProjectRepository $projectRepository, $id)
    {

        $theMember = $memberRepository->findOneBy([
            'emailAddress' =>  $request->get('memberEmail')
        ]);

        $myProject = $projectRepository->findOneBy([
            'id' => $id
        ]);
        
        $success = null;
        $error = null;
        try {
            if($theMember) {
                $invitationService->inviteUser($theMember, $myProject);
            } else {
                throw new \RuntimeException("Ce membre n'apparait pas dans nos registres...");
            }
            $success = "Invitation envoyé avec succés";
        } catch(\Exception $e) {
            $error = $e->getMessage();
        }

        $owner = $myProject->getOwner();
        return $this->render('project/project_details.html.twig', [
            "success"=> $success,
            "error"=> $error,
            'project' => $myProject,
            'owner' => $owner,
            'members' => $myProject->getMembers(),
            'user' => $this->getUser()
        ]);
    }

}
