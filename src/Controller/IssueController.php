<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Form\IssueType;
use App\Entity\Issue;
use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;

class IssueController extends AbstractController {
    /**
     * @Route("/project/{id_project}/new_issue", name = "createIssue")
     */
    public function viewCreationIssue(Request $request, ProjectRepository $projectRepository, $id_project) : Response
    {
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);

        $error = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];
            $description= $data['description'];
            $difficulty=$data['difficulty'];
            $priority=$data['priority'];
            $status=$data['status'];
            $myProject = $projectRepository->findOneBy([
                'id' => $id_project
            ]);

            $issue = new Issue($name,$description,$difficulty,$priority,$status,$myProject);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            if($error == ''){
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('issue/issue_form.html.twig', ["form"=> $form->createView(),
                                                                    'user' => $this->getUser()] );

    }

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
