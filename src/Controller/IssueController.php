<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Entity\Issue;
use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;

class IssueController extends AbstractController {
    
    /**
     * @Route("/project/{id_project}/issues", name = "issuesList", methods = {"GET"})
     */
    public function viewIssues(Request $request, ProjectRepository $projectRepository, $id_project) {
        $member = $this->getUser();
    
        $myProject = $projectRepository->findOneBy([
            'id' => $id_project
        ]);

        $myIssues = $myProject->getIssues();

        return $this->render('issue/issue_list.html.twig', ["myProject"=> $myProject, 
                                                            "myIssues"=> $myIssues,
                                                            'user' => $this->getUser()]);
    }

}
