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

    public function __construct(TaskRepository $taskRepository, NotificationService $notifications,
                                ProjectRepository $projectRepository, SprintRepository $sprintRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->sprintRepository=$sprintRepository;
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks", name="tasksList", methods={"GET"})
     */
    public function viewTasks($id_project,$id_sprint)
    {
        $project = $this->projectRepository->find($id_project);
        $sprint=$this->sprintRepository->find($id_sprint);
        $todos = $this->taskRepository->getToDo($sprint);
        $doings = $this->taskRepository->getDoing($sprint);
        $dones = $this->taskRepository->getDone($sprint);

        $manDaysStat = $this->taskRepository->getProportionEstimationManDays( $sprint);
        $statusStat = $this->taskRepository->getProportionStatus($sprint);
        $memberStat = $this->taskRepository->getProportionMembersAssociated($sprint);
        $memberMansDayStat = $this->taskRepository->getProportionMansDPerMembersAssociated($sprint);

        return $this->render('task/task_list.html.twig', [
            'project' => $project,
            'sprint' => $sprint,
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
     * @Route("/project/{id_project}/sprints/{id_sprint}/tasks/new", name="createTask")
     */
    public function createTask(Request $request, EntityManagerInterface $entityManager, $id_project,$id_sprint)
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
                $entityManager->persist($task);
                $entityManager->flush();

                foreach($relatedIssues as $issue) {
                    $entityManager->persist($issue);
                    $entityManager->flush();
                }
                $this->notifications->addSuccess("Tâche {$task->getNumber()} créée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }

            return $this->redirectToRoute('sprint/sprintDetails', [
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
    public function editTask(Request $request, EntityManagerInterface $entityManager,$id_sprint, $id_project, $id_task)
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
                $entityManager->persist($task);
                $entityManager->flush();

                foreach($task->getRelatedIssues() as $issue) {
                    $entityManager->persist($issue);
                    $entityManager->flush();
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
    public function deleteTask(EntityManagerInterface $entityManager, $id_sprint,$id_project, $id_task)
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
                $entityManager->remove($task);
                $entityManager->flush();

                foreach($task->getRelatedIssues() as $issue) {
                    $issue->removeTask($task);
                    $entityManager->persist($issue);
                    $entityManager->flush();
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
    public function changeTaskStatus(EntityManagerInterface $entityManager, $id_project,$id_sprint, $id_task, $status)
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
            $entityManager->flush();
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
