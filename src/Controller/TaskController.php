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
    /**
     * @Route("/project/{id_project}/tasks", name="tasksList", methods={"GET"})
     */
    public function viewTasks(ProjectRepository $projectRepository, TaskRepository $taskRepository, $id_project)
    {
        $project = $projectRepository->find($id_project);

        $todos = $taskRepository->getToDo($project);
        $doings = $taskRepository->getDoing($project);
        $dones = $taskRepository->getDone($project);

        return $this->render('task/task_list.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'todos' => $todos,
            'doings' => $doings,
            'dones' => $dones
        ]);
    }

    /**
     * @Route("/project/{id_project}/tasks/new", name="createTask")
     */
    public function createTask(Request $request, ProjectRepository $projectRepository,
                               EntityManagerInterface $entityManager, TaskRepository $taskRepository, $id_project)
    {
        $project = $projectRepository->find($id_project);
        $nextNumber = $taskRepository->getNextNumber($project);
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
    public function editTask(Request $request, ProjectRepository $projectRepository, TaskRepository $taskRepository,
                             EntityManagerInterface $entityManager, $id_project, $id_task)
    {
        $project = $projectRepository->find($id_project);
        $task = $taskRepository->findOneBy([
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
    public function deleteTask(ProjectRepository $projectRepository, TaskRepository $taskRepository,
                               EntityManagerInterface $entityManager, NotificationService $notifications,
                               $id_project, $id_task)
    {
        $project = $projectRepository->find($id_project);
        $task = $taskRepository->findOneBy([
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
    public function changeTaskStatus(ProjectRepository $projectRepository, TaskRepository $taskRepository,
                                     NotificationService $notifications, EntityManagerInterface $entityManager,
                                     $id_project, $id_task, $status)
    {
        $project = $projectRepository->find($id_project);
        $task = $taskRepository->findOneBy([
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
