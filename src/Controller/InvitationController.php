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
       
        $invitationService->inviteUser($theMember, $myProject);

        return $this->redirectToRoute('dashboard');
    }

}
