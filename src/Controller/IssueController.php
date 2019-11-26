<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Form\IssueType;
use App\Entity\Issue;
use App\Repository\IssueRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RenderService;

class IssueController extends AbstractController {

    private $issueRepository;
    private $notifications;
    private $projectRepository;

    public function __construct(IssueRepository $issueRepository, NotificationService $notifications, ProjectRepository $projectRepository)
    {
        $this->issueRepository = $issueRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/project/{id_project}/issues/new", name="createIssue")
     */
    public function viewCreationIssue(Request $request, EntityManagerInterface $entityManager, $id_project) : Response
    {
        $project = $this->projectRepository->find( $id_project);
        $nextNumber = $this->issueRepository->getNextNumber($project);
        $form = $this->createForm(IssueType::class, ['number' => $nextNumber], [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $description= $data['description'];
                $difficulty=$data['difficulty'];
                $priority=$data['priority'];
                $status=$data['status'];
                $sprint = $data['sprint'];
                $issue = new Issue($nextNumber, $description, $difficulty, $priority, $status, $sprint, $project);
                $entityManager->persist($issue);
                $entityManager->flush();
                $this->notifications->addSuccess("Issue {$issue->getNumber()} créée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            
            return $this->redirectToRoute('issuesList', [
                'id_project' => $id_project
            ]);
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
    public function viewIssues(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $issues = $project->getIssues();
        $statusStat = $this->issueRepository->getProportionStatus( $project);
        $diffStat = $this->issueRepository->getProportionDifficulty( $project);
        $prioStat = $this->issueRepository->getProportionPriority( $project);

        return $this->render('issue/issue_list.html.twig', [
            'project'=> $project,
            'issues' => $issues,
            'statusStat' => $statusStat,
            'diffStat' => $diffStat,
            'prioStat' => $prioStat,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id_project}/issues/{id_issue}/edit", name="editIssue")
     */
    public function editIssue(Request $request, EntityManagerInterface $entityManager, $id_issue, $id_project): Response
    {
        $issue = $this->issueRepository->find($id_issue);
        $project = $this->projectRepository->find( $id_project);

        $form = $this->createForm(IssueType::class, $issue, [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {     
            try {
                $entityManager->persist($issue);
                $entityManager->flush();
                $this->notifications->addSuccess("Issue {$issue->getNumber()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            
            return $this->redirectToRoute('issuesList', [
                'id_project' => $id_project
            ]);
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
    public function deleteIssue(Request $request, EntityManagerInterface $entityManager, $id_project, $id_issue)
    {
        $issue = $this->issueRepository->find($id_issue);

        if (!$issue) {
            $this->notifications->addError("Aucune issue n'existe avec l'id {$id_issue}");
        } else {
            try {
                $entityManager->remove($issue);
                $entityManager->flush();
                $this->notifications->addSuccess("Issue {$issue->getNumber()} supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->redirectToRoute('issuesList', [
            'id_project' => $id_project
        ]);
    }
}
