<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\PlanningPoker;
use App\EntityException\InvalidStatusTransitionException;
use App\Form\TaskType;
use App\Form\PlanningPokerType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\PlanningPokerRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    
    private $taskRepository;
    private $notifications;
    private $projectRepository;

    public function __construct(TaskRepository $taskRepository, NotificationService $notifications, ProjectRepository $projectRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/project/{id_project}/tasks", name="tasksList", methods={"GET"})
     */
    public function viewTasks($id_project)
    {
        $project = $this->projectRepository->find($id_project);

        $todos = $this->taskRepository->getToDo($project);
        $doings = $this->taskRepository->getDoing($project);
        $dones = $this->taskRepository->getDone($project);

        $manDaysStat = $this->taskRepository->getProportionEstimationManDays( $project);
        $statusStat = $this->taskRepository->getProportionStatus( $project);
        $memberStat = $this->taskRepository->getProportionMembersAssociated( $project);
        $memberMansDayStat = $this->taskRepository->getProportionMansDPerMembersAssociated($project);

        return $this->render('task/task_list.html.twig', [
            'project' => $project,
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
     * @Route("/project/{id_project}/tasks/new", name="createTask")
     */
    public function createTask(Request $request, EntityManagerInterface $entityManager, $id_project)
    {
        $project = $this->projectRepository->find($id_project);
        $nextNumber = $this->taskRepository->getNextNumber($project);
        $form = $this->createForm(TaskType::class, ['number' => $nextNumber], [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $number = $nextNumber;
                $description = $data['description'];
                $requiredManDays = 0;
                $developper = $data['developper'];
                $relatedIssues = $data['relatedIssues']->toArray();

                $task = new Task($number, $description, $requiredManDays, $relatedIssues, $project, $developper);
                $entityManager->persist($task);
                $entityManager->flush();
                foreach($project->getMembers() as $member) {
                    $planningPoker = new PlanningPoker($task, $member);
                    if($member->getId() == $this->getUser()->getId() ){
                        $planningPoker->setValue($data['requiredManDays']);
                    }
                    $entityManager->persist($planningPoker);
                    $entityManager->flush();
                }
                $member = $project->getOwner();
                $planningPoker = new PlanningPoker($task, $member);
                if($member->getId() == $this->getUser()->getId() ){
                    $planningPoker->setValue($data['requiredManDays']);
                }
                $entityManager->persist($planningPoker);
                $entityManager->flush();

                $this->notifications->addSuccess("Tâche {$task->getNumber()} créée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }

            return $this->redirectToRoute('tasksList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('task/task_form.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/{id_task}/edit", name="editTask")
     */
    public function editTask(Request $request, EntityManagerInterface $entityManager, $id_project, $id_task)
    {
        $project = $this->projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);
        $form = $this->createForm(TaskType::class, $task, [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($task);
                $entityManager->flush();
                $this->notifications->addSuccess("Tâche {$task->getNumber()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('tasksList', [
                'id_project' => $project->getId()
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/{id_task}/delete", name="deleteTask")
     */
    public function deleteTask(EntityManagerInterface $entityManager, $id_project, $id_task)
    {
        $project = $this->projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);

        if (!$task) {
            $this->notifications->addError("Aucune tâche n'existe avec l'id {$id_task}");
        } else {
            try {
                $entityManager->remove($task);
                $entityManager->flush();
                $this->notifications->addSuccess("Tâche {$task->getNumber()} supprimée avec succès.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('tasksList', [
            'id_project' => $id_project
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/{id_task}/{status}", name="changeTaskStatus", requirements={
     *     "status"="^doing|done$"
     * })
     */
    public function changeTaskStatus(EntityManagerInterface $entityManager, $id_project, $id_task, $status)
    {
        $project = $this->projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);

        try {
            if ($status == Task::DOING) {
                $task->begin();
            }
            if ($status == Task::DONE) {
                $task->finish();
            }
            $entityManager->flush();
        }
        catch (InvalidStatusTransitionException $e) {
            $this->notifications->addError($e->getMessage());
        }

        return $this->redirectToRoute('tasksList', [
            'id_project' => $id_project
        ]);
    }


    /**
     * @Route("/project/{id_project}/tasks/{id_task}/plannigPoker", name="planningPoker")
     */
    public function plannigPokerForTask(Request $request, EntityManagerInterface $entityManager, 
        PlanningPokerRepository $planningPokerRepository,$id_project, $id_task)
    {
        $project = $this->projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);
        $planningPoker = $planningPokerRepository->findOneBy([
            'member' => $this->getUser(),
            'task' => $task,
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
                $this->notifications->addSuccess("Tâche {$task->getNumber()} évaluée avec succés.");

                if($planningPokerRepository->isPlanningPokerDoneByTask($task) ) {
                    $cpt = 0;
                    $amount = 0;
                    foreach($planningPokerRepository->getPlanningPokerByTask($task) as $planningPoker) {
                        ++$cpt;
                        $amount += $planningPoker->getValue();
                        $entityManager->remove($planningPoker);
                        $entityManager->flush();
                    }
                }
                $task->setRequiredManDays($amount / $cpt);
                $entityManager->persist($task);
                $entityManager->flush();
                $this->notifications->addSuccess("Fin du planning poker pour la tâche {$task->getNumber()}.");
                return $this->redirectToRoute('tasksList', [
                    'id_project' => $id_project
                ]);
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->render('task/planning_poker_form.html.twig', [
            'project' => $project,
            'task' => $task,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
