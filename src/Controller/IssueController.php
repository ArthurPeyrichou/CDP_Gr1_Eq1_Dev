<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Form\IssueType;
use App\Entity\Issue;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RenderService;

class IssueController extends AbstractController {

    /**
     * @Route("/project/{id_project}/issues/new", name="createIssue")
     */
    public function viewCreationIssue(Request $request, ProjectRepository $projectRepository,
                                      EntityManagerInterface $entityManager, $id_project) : Response
    {
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);
        $project = $projectRepository->find( $id_project);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null; 
            $success = null; 
            try {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $difficulty=$data['difficulty'];
                $priority=$data['priority'];
                $status=$data['status'];
                $issue = new Issue($name, $description, $difficulty, $priority, $status, $project);
                $success =  "Issue {$issue->getName()} créée avec succés."; 
                $entityManager->persist($issue);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            
            return $this->renderIssue($error, $success , $project);
        }

        return $this->render('issue/issue_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);

    }

    /**
     * @Route("/project/{id_project}/issues", name="issuesList", methods={"GET"})
     */
    public function viewIssues(Request $request, ProjectRepository $projectRepository, $id_project) {
        return $this->renderIssue(null, null, $projectRepository->find($id_project));
    }

    /**
     * @Route("/project/{id_project}/issues/{id_issue}/edit", name="editIssue")
     */
    public function editIssue(Request $request, EntityManagerInterface $entityManager,
                              ProjectRepository $projectRepository, IssueRepository $issueRepository,
                              $id_issue, $id_project): Response
    {
        $issue = $issueRepository->find($id_issue);
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);
        $project = $projectRepository->find($id_project);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {     
            try {
                $entityManager->persist($issue);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            return $this->renderIssue($error, "Issue {$issue->getName()} éditée avec succés.", $project);
        }

        return $this->render('issue/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id_project}/issues/{id_issue}/delete", name="deleteIssue")
     */
    public function deleteIssue(Request $request, IssueRepository $issue_Repository,
                                EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $id_project, $id_issue)
    {
        $issue = $issue_Repository->find($id_issue);
        $error = null;
        $success = null;
        if (!$issue) {
            $error ="Aucune issue n'existe avec l'id {$id_issue}";
        } else {
            try {
                $success = "Issue {$issue->getName()} supprimée avec succés.";
                $entityManager->remove($issue);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return $this->renderIssue($error, $success, $projectRepository->find($id_project));
    }

    private function renderIssue($error, $success, $project) {
        
        $issues = $project->getIssues();

        return $this->render('issue/issue_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'issues' => $issues,
            'user' => $this->getUser()
        ]);
    }
}
