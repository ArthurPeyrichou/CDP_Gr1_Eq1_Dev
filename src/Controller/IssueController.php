<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Entity\Issue;
use App\Entity\PlanningPoker;
use App\Entity\Task;
use App\Entity\Test;
use App\Form\IssueType;
use App\Form\PlanningPokerType;
use App\Repository\IssueRepository;
use App\Repository\PlanningPokerRepository;
use App\Repository\TaskRepository;
use App\Repository\TestRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class IssueController extends AbstractController {

    private $issueRepository;
    private $notifications;
    private $projectRepository;
    private $entityManager;

    public function __construct(IssueRepository $issueRepository, NotificationService $notifications, ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        $this->issueRepository = $issueRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Displays and handles the issue creation form.
     * @Route("/project/{id_project}/issues/new", name="createIssue")
     */
    public function viewCreationIssue(Request $request, $id_project) : Response
    {
        $project = $this->projectRepository->find( $id_project);
        $nextNumber = $this->issueRepository->getNextNumber($project);

        $form = $this->createForm(IssueType::class, ['number' => $nextNumber], [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $difficulty = $data['difficulty'];
            if (count($project->getMembers()) > 0) {
                $difficulty = 0;
            }
            $issue = new Issue($nextNumber, $data['description'], $difficulty, $data['priority'], $project, $data['sprints']);
            $this->entityManager->persist($issue);

            if (count($project->getMembers()) > 0) {
                foreach($project->getMembersAndOwner() as $member) {
                    $planningPoker = new PlanningPoker($issue, $member);
                    if ($member->getId() == $this->getUser()->getId()){
                        $planningPoker->setValue($data['difficulty']);
                    }
                    $this->entityManager->persist($planningPoker);
                }
            }

            $this->entityManager->flush();
            $this->notifications->addSuccess("Issue {$issue->getNumber()} créée avec succés.");

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
     * @Route("/project/{id_project}/issues/{id_issue}/tests", name="issueDetailsTest", methods={"GET"})
     */
    public function viewIssueTest(Request $request, $id_project,$id_issue, TestRepository $testRepository): Response
    {
        $todos = [];
        $faileds = [];
        $succeededs = [];
        $issue = $this->issueRepository->find($id_issue);
        $tests = $issue->getTests();
        foreach ($tests as $test) {
            switch($test->getState()) {
                case Test::TODO:
                    $todos[] = $test;
                    break;
                case Test::FAILED:
                    $faileds[] = $test;
                    break;
                case Test::SUCCEEDED:
                    $succeededs[] = $test;
                    break;
                default:
                    break;
            }
        }
        $project = $this->projectRepository->find( $id_project);
        $statusStat = $testRepository->getProportionStatusByIssue($project, $issue);

        return $this->render('issue/issue_details.html.twig', [
            'project' => $project,
            'issue' => $issue,
            'todos' => $todos,
            'faileds'=> $faileds,
            'succeededs'=>$succeededs,
            'statusStat'=>$statusStat,
            'user' => $this->getUser(),

        ]);
    }

    /**
     * Displays the issue list page.
     * @Route("/project/{id_project}/issues", name="issuesList", methods={"GET"})
     */
    public function viewIssues(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);

        $statusStat = $this->issueRepository->getProportionStatus( $project);
        $diffStat = $this->issueRepository->getProportionDifficulty( $project);
        $prioStat = $this->issueRepository->getProportionPriority( $project);

        return $this->render('issue/issue_list.html.twig', [
            'project'=> $project,
            'issues' => $project->getIssues(),
            'statusStat' => $statusStat,
            'diffStat' => $diffStat,
            'prioStat' => $prioStat,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Displays and handles the issue edit form.
     * @Route("/project/{id_project}/issues/{id_issue}/edit", name="editIssue")
     */
    public function editIssue(Request $request, $id_issue, $id_project): Response
    {
        $issue = $this->issueRepository->find($id_issue);
        $project = $this->projectRepository->find( $id_project);

        $form = $this->createForm(IssueType::class, $issue, [
            IssueType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($issue);
                $this->entityManager->flush();
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
     * Displays the issue details page.
     * @Route("/project/{id_project}/issues/{id_issue}/tasks", name="issueDetailsTask", methods={"GET"})
     */
    public function viewIssueTask(Request $request, TaskRepository $taskRepository, $id_project,$id_issue): Response
    {
        $issue=$this->issueRepository->find($id_issue);
        $todos = [];
        $doings = [];
        $dones = [];
        foreach($issue->getTasks() as $task){
            switch($task->getStatus()){
                case Task::TODO:
                    $todos[] = $task;
                    break;
                case Task::DOING:
                    $doings[] = $task;
                    break;
                case Task::DONE:
                    $dones[] = $task;
                    break;
                default:
                    break;
            }
        }

        $manDaysStat = $taskRepository->getProportionEstimationManDaysByIssue($issue);
        $statusStat = $taskRepository->getProportionStatusByIssue($issue);
        $memberStat = $taskRepository->getProportionMembersAssociatedByIssue($issue);
        $memberMansDayStat = $taskRepository->getProportionMansDPerMembersAssociatedByIssue($issue);

        return $this->render('issue/issue_details.html.twig', [
            'project' => $this->projectRepository->find($id_project),
            'issue' => $issue,
            'user' => $this->getUser(),
            'manDaysStat' => $manDaysStat,
            'statusStat' => $statusStat,
            'memberStat' => $memberStat,
            'memberMansDayStat' => $memberMansDayStat,
            'todos' => $todos,
            'doings' => $doings,
            'dones' => $dones
        ]);
    }

    /**
     * Handles the deletion of an issue.
     * @Route("/project/{id_project}/issues/{id_issue}/delete", name="deleteIssue")
     */
    public function deleteIssue(Request $request, PlanningPokerRepository $planningPokerRepository, $id_project, $id_issue)
    {
        $issue = $this->issueRepository->find($id_issue);

        if (!$issue) {
            $this->notifications->addError("Aucune issue n'existe avec l'id {$id_issue}");
        } else {
            try {

                foreach($issue->getTasks() as $task) {
                    $task->removeRelatedIssue($issue);
                    $this->entityManager->persist($task);
                    $this->entityManager->flush();
                }
                foreach($planningPokerRepository->getPlanningPokerByIssue($issue) as $planningPoker) {
                    $this->entityManager->remove($planningPoker);
                    $this->entityManager->flush();
                }

                $this->entityManager->remove($issue);
                $this->entityManager->flush();

                $this->notifications->addSuccess("Issue {$issue->getNumber()} supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->redirectToRoute('issuesList', [
            'id_project' => $id_project
        ]);
    }

    /**
     * Displays and handles the planning poker form for an issue.
     * @Route("/project/{id_project}/issues/{id_issue}/plannigPoker", name="planningPoker")
     */
    public function plannigPokerForIssue(Request $request, PlanningPokerRepository $planningPokerRepository, $id_project, $id_issue)
    {
        $project = $this->projectRepository->find($id_project);
        $member = $this->getUser();
        $issue = $this->issueRepository->findOneBy([
            'id' => $id_issue,
            'project' => $project
        ]);
        $planningPoker = $planningPokerRepository->findOneBy([
            'member' => $member,
            'issue' => $issue,
        ]);

        $form = $this->createForm(PlanningPokerType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $value = $data['value'];
                $planningPoker->setValue($value);
                $this->entityManager->persist($planningPoker);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Issue {$issue->getNumber()} évaluée avec succés.");

                if($planningPokerRepository->isPlanningPokerDoneByIssue($issue) ) {
                    $this->calculateIssueDifficultyFromPP($planningPokerRepository, $issue);
                    $this->notifications->notifAllmemberFromProject($this->entityManager, $member, $project, "Fin du planning poker pour l'issue {$issue->getNumber()}.");
                }

                return $this->redirectToRoute('issuesList', [
                    'id_project' => $id_project
                ]);
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->render('issue/planning_poker_form.html.twig', [
            'project' => $project,
            'issue' => $issue,
            'user' => $member,
            'form' => $form->createView()
        ]);
    }

    public function calculateIssueDifficultyFromPP(PlanningPokerRepository $planningPokerRepository, Issue $issue){
        $cpt = 0;
        $amount = 0;
        foreach($planningPokerRepository->getPlanningPokerByIssue($issue) as $planningPoker) {
            ++$cpt;
            $amount += $planningPoker->getValue();
            $this->entityManager->remove($planningPoker);
            $this->entityManager->flush();
        }
        if($cpt <= 0) {
            $issue->setDifficulty(0);
        } else {
            $issue->setDifficulty($amount / $cpt);
        }
        $this->entityManager->persist($issue);
        $this->entityManager->flush();
    }
}
