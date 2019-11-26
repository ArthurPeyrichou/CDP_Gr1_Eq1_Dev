<?php

namespace App\Controller;

use App\Entity\Task;
use App\EntityException\InvalidStatusTransitionException;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    
    private $taskRepository;

    public function __construct( TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/project/{id_project}/tasks", name="tasksList", methods={"GET"})
     */
    public function viewTasks(ProjectRepository $projectRepository, $id_project)
    {
        $project = $projectRepository->find($id_project);

        $todos = $this->taskRepository->getToDo($project);
        $doings = $this->taskRepository->getDoing($project);
        $dones = $this->taskRepository->getDone($project);

        $manDaysStat = $this->taskRepository->getProportionEstimationManDays( $project);
        $statusStat = $this->taskRepository->getProportionStatus( $project);
        $memberStat = $this->taskRepository->getProportionMembersAssociated( $project);

        return $this->render('task/task_list.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'manDaysStat' => $manDaysStat,
            'statusStat' => $statusStat,
            'memberStat' => $memberStat,
            'todos' => $todos,
            'doings' => $doings,
            'dones' => $dones
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/new", name="createTask")
     */
    public function createTask(Request $request, ProjectRepository $projectRepository,
                               EntityManagerInterface $entityManager, $id_project)
    {
        $project = $projectRepository->find($id_project);
        $nextNumber = $this->taskRepository->getNextNumber($project);
        $form = $this->createForm(TaskType::class, ['number' => $nextNumber], [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $number = $nextNumber;
            $description = $data['description'];
            $requiredManDays = $data['requiredManDays'];
            $developper = $data['developper'];
            $relatedIssues = $data['relatedIssues']->toArray();

            $task = new Task($number, $description, $requiredManDays, $relatedIssues, $project, $developper);

            $entityManager->persist($task);
            $entityManager->flush();

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
    public function editTask(Request $request, ProjectRepository $projectRepository,
                             EntityManagerInterface $entityManager, $id_project, $id_task)
    {
        $project = $projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);
        $form = $this->createForm(TaskType::class, $task, [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
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
    public function deleteTask(ProjectRepository $projectRepository,
                               EntityManagerInterface $entityManager, NotificationService $notifications,
                               $id_project, $id_task)
    {
        $project = $projectRepository->find($id_project);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'project' => $project
        ]);

        if (!$task) {
            $notifications->addError("Aucune tâche n'existe avec l'id {$id_task}");
        } else {
            $entityManager->remove($task);
            $entityManager->flush();
            $notifications->addSuccess("Tâche {$task->getNumber()} supprimée avec succès.");
        }
        return $this->redirectToRoute('tasksList', [
            'id_project' => $project->getId()
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/{id_task}/{status}", name="changeTaskStatus", requirements={
     *     "status"="^doing|done$"
     * })
     */
    public function changeTaskStatus(ProjectRepository $projectRepository,
                                     NotificationService $notifications, EntityManagerInterface $entityManager,
                                     $id_project, $id_task, $status)
    {
        $project = $projectRepository->find($id_project);
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
            $notifications->addError($e->getMessage());
        }

        return $this->redirectToRoute('tasksList', [
            'id_project' => $project->getId()
        ]);
    }
}
