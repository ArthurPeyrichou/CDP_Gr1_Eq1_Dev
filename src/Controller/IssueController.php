<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Entity\Issue;
use App\Entity\PlanningPoker;
use App\Form\IssueType;
use App\Form\PlanningPokerType;
use App\Repository\IssueRepository;
use App\Repository\PlanningPokerRepository;
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
                $difficulty= 0;
                $priority=$data['priority'];
                $sprint = $data['sprint'];
                if(count($project->getMembers()) == 0) {
                    $difficulty = $data['difficulty'];
                }
                $issue = new Issue($nextNumber, $description, $difficulty, $priority, $project, $sprint);
                $entityManager->persist($issue);
                $entityManager->flush();

                if(count($project->getMembers()) > 0) {
                    foreach($project->getMembers() as $member) {
                        $planningPoker = new PlanningPoker($issue, $member);
                        if($member->getId() == $this->getUser()->getId() ){
                            $planningPoker->setValue($data['difficulty']);
                        }
                        $entityManager->persist($planningPoker);
                        $entityManager->flush();
                    }
                    $member = $project->getOwner();
                    $planningPoker = new PlanningPoker($issue, $member);
                    if($member->getId() == $this->getUser()->getId() ){
                        $planningPoker->setValue($data['difficulty']);
                    }
                    $entityManager->persist($planningPoker);
                    $entityManager->flush();
                }

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

                foreach($issue->getTasks() as $task) {
                    $task->removeRelatedIssue($issue);
                    $entityManager->persist($task);
                    $entityManager->flush();
                }
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
     * @Route("/project/{id_project}/issues/{id_issue}/plannigPoker", name="planningPoker")
     */
    public function plannigPokerForIssue(Request $request, EntityManagerInterface $entityManager, 
        PlanningPokerRepository $planningPokerRepository,$id_project, $id_issue)
    {
        $project = $this->projectRepository->find($id_project);
        $issue = $this->issueRepository->findOneBy([
            'id' => $id_issue,
            'project' => $project
        ]);
        $planningPoker = $planningPokerRepository->findOneBy([
            'member' => $this->getUser(),
            'issue' => $issue,
        ]);

        $form = $this->createForm(PlanningPokerType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $value = $data['value'];
                $planningPoker->setValue($value);
                $entityManager->persist($planningPoker);
                $entityManager->flush();
                $this->notifications->addSuccess("Issue {$issue->getNumber()} évaluée avec succés.");

                if($planningPokerRepository->isPlanningPokerDoneByIssue($issue) ) {
                    $cpt = 0;
                    $amount = 0;
                    foreach($planningPokerRepository->getPlanningPokerByIssue($issue) as $planningPoker) {
                        ++$cpt;
                        $amount += $planningPoker->getValue();
                        $entityManager->remove($planningPoker);
                        $entityManager->flush();
                    }
                    $issue->setDifficulty($amount / $cpt);
                    $entityManager->persist($issue);
                    $entityManager->flush();
                    $this->notifications->addSuccess("Fin du planning poker pour l'issue {$issue->getNumber()}.");
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
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
