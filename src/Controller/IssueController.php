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

    private $issueRepository;

    public function __construct( IssueRepository $issueRepository)
    {
        $this->issueRepository = $issueRepository;
    }

    /**
     * @Route("/project/{id_project}/issues/new", name="createIssue")
     */
    public function viewCreationIssue(Request $request, ProjectRepository $projectRepository,
                                      EntityManagerInterface $entityManager,
                                      $id_project) : Response
    {
        $project = $projectRepository->find( $id_project);
        $nextNumber = $this->issueRepository->getNextNumber($project);
        $form = $this->createForm(IssueType::class, ['number' => $nextNumber], [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null; 
            $success = null; 
            try {
                $data = $form->getData();
                $description= $data['description'];
                $difficulty=$data['difficulty'];
                $priority=$data['priority'];
                $status=$data['status'];
                $sprint = $data['sprint'];
                $issue = new Issue($nextNumber, $description, $difficulty, $priority, $status, $sprint, $project);
                $success = "Issue {$issue->getNumber()} créée avec succés.";
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
                              ProjectRepository $projectRepository,
                              $id_issue, $id_project): Response
    {
        $issue = $this->issueRepository->find($id_issue);
        $project = $projectRepository->find( $id_project);
        $nextNumber = $this->issueRepository->getNextNumber($project);
        $form = $this->createForm(IssueType::class, $issue, [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {     
            try {
                $entityManager->persist($issue);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            return $this->renderIssue($error, "Issue {$issue->getNumber()} éditée avec succés.", $project);
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
                $success = "Issue {$issue->getNumber()} supprimée avec succés.";
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
        $statusStat = $this->issueRepository->getProportionStatus( $project);
        $diffStat = $this->issueRepository->getProportionDifficulty( $project);
        $prioStat = $this->issueRepository->getProportionPriority( $project);

        return $this->render('issue/issue_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'issues' => $issues,
            'statusStat' => $statusStat,
            'diffStat' => $diffStat,
            'prioStat' => $prioStat,
            'user' => $this->getUser()
        ]);
    }
}
