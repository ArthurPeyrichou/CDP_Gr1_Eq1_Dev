<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Entity\Issue;
use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController {
    
    /**
     * @Route("/project/{id_project}/issues", name = "issuesList", methods = {"GET"})
     */
    public function viewIssues(Request $request) {
        $member = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Project::class);
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
