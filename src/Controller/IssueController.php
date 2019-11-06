<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\INVITATION;
use App\Entity\PROJECT;
use App\Entity\ISSUE;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController {
    
    /**
     * @Route("/project/{id_project}/issues", name = "issuesList", methods = {"GET"})
     */
    public function viewIssues(Request $request) {
        $member = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(PROJECT::class);
        $myProject = $repository->findOneBy([
            'id' => $request->attributes->get('id_project')
        ]);
        $repository = $this->getDoctrine()->getRepository(ISSUE::class);
        $myIssues = $repository->findBy([
            'PROJECT_ID' => $request->attributes->get('id_project')
        ]);
        $pseudo = $member->getName();

        return $this->render('issue/issue_list.html.twig', ["myProject"=> $myProject, 
                                                            "myIssues"=> $myIssues,
                                                            "pseudo"=> $pseudo]);
    }

}
