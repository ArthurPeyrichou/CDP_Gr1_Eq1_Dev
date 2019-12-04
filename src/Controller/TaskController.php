<?php

namespace App\Controller;

use App\Entity\Task;
use App\EntityException\InvalidStatusTransitionException;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\SprintRepository;
use App\Repository\TaskRepository;
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
    private $sprintRepository;
    private $entityManager;

    public function __construct(TaskRepository $taskRepository, NotificationService $notifications,
                                ProjectRepository $projectRepository, SprintRepository $sprintRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->sprintRepository=$sprintRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks/new", name="createTask")
     */
    public function createTask(Request $request, $id_project,$id_sprint)
    {
        $project = $this->projectRepository->find($id_project);
        $sprint = $this->sprintRepository->find($id_sprint);
        $nextNumber = $this->taskRepository->getNextNumber($sprint);
        $form = $this->createForm(TaskType::class, ['number' => $nextNumber], [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $number = $nextNumber;
                $description = $data['description'];
                $requiredManDays = $data['requiredManDays'];
                $developper = $data['developper'];
                $relatedIssues = $data['relatedIssues']->toArray();

                $task = new Task($number, $description, $requiredManDays, $relatedIssues, $developper,$sprint);
                $this->entityManager->persist($task);
                $this->entityManager->flush();

                foreach($relatedIssues as $issue) {
                    $this->entityManager->persist($issue);
                    $this->entityManager->flush();
                }
                $this->notifications->addSuccess("Tâche {$task->getNumber()} créée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }

            return $this->redirectToRoute('sprintDetails', [
                'id_project' => $id_project,
                'id_sprint' => $id_sprint
            ]);
        }

        return $this->render('task/task_form.html.twig', [
            'project' => $project,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks/{id_task}/edit", name="editTask")
     */
    public function editTask(Request $request, $id_sprint, $id_project, $id_task)
    {
        $project = $this->projectRepository->find($id_project);
        $sprint= $this->sprintRepository->find($id_sprint);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'sprint' => $sprint
        ]);
        $form = $this->createForm(TaskType::class, $task, [
            TaskType::PROJECT => $project
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($task);
                $this->entityManager->flush();

                foreach($task->getRelatedIssues() as $issue) {
                    $this->entityManager->persist($issue);
                    $this->entityManager->flush();
                }
                $this->notifications->addSuccess("Tâche {$task->getNumber()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('sprintDetails', [
                'id_project' => $id_project,
                'id_sprint' => $id_sprint
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'project' => $project,
            'sprint' => $sprint,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks/{id_task}/delete", name="deleteTask")
     */
    public function deleteTask($id_sprint,$id_project, $id_task)
    {
        $sprint= $this->sprintRepository->find($id_sprint);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'sprint' => $sprint
        ]);

        if (!$task) {
            $this->notifications->addError("Aucune tâche n'existe avec l'id {$id_task}");
        } else {
            try {
                $this->entityManager->remove($task);
                $this->entityManager->flush();

                foreach($task->getRelatedIssues() as $issue) {
                    $issue->removeTask($task);
                    $this->entityManager->persist($issue);
                    $this->entityManager->flush();
                }
                $this->notifications->addSuccess("Tâche {$task->getNumber()} supprimée avec succès.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('sprintDetails', [
            'id_project' => $id_project,
            'id_sprint' => $id_sprint
        ]);
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks/{id_task}/{status}", name="changeTaskStatus", requirements={
     *     "status"="^doing|done$"
     * })
     */
    public function changeTaskStatus($id_project,$id_sprint, $id_task, $status)
    {
        $sprint= $this->sprintRepository->find($id_sprint);
        $task = $this->taskRepository->findOneBy([
            'id' => $id_task,
            'sprint' => $sprint
        ]);

        try {
            if ($status == Task::DOING) {
                $task->begin();
            }
            if ($status == Task::DONE) {
                $task->finish();
            }
            $this->entityManager->flush();
        }
        catch (InvalidStatusTransitionException $e) {
            $this->notifications->addError($e->getMessage());
        }

        return $this->redirectToRoute('sprintDetails', [
            'id_project' => $id_project,
            'id_sprint' => $id_sprint
        ]);
    }

}
