<?php
// src/Controller/SprintController.php
namespace App\Controller;

use App\Form\SprintType;
use App\Entity\Sprint;
use App\Repository\SprintRepository;
use App\Repository\IssueRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class SprintController extends AbstractController {

    private $sprintRepository;
    private $notifications;
    private $projectRepository;

    public function __construct(SprintRepository $sprintRepository, NotificationService $notifications, ProjectRepository $projectRepository)
    {
        $this->sprintRepository = $sprintRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/project/{id_project}/sprints/new", name="createSprint")
     */
    public function viewCreationSprint(Request $request, EntityManagerInterface $entityManager, $id_project) : Response
    {
        $project = $this->projectRepository->find($id_project);
        $nextNumber = $this->sprintRepository->getNextNumber($project);
        $form = $this->createForm(SprintType::class, ['number' => $nextNumber]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $description= $data['description'];
                $startDate=$data['startDate'];
                $estimated_duration=$data['estimatedDuration'];
                $sprint = new Sprint($project, $nextNumber, $description, $startDate, $estimated_duration);
                $entityManager->persist($sprint);
                $entityManager->flush();
                $this->notifications->addSuccess("Sprint {$sprint->getNumber()} créée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('sprintsList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('sprint/sprint_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id_project}/sprints", name="sprintsList", methods={"GET"})
     */
    public function viewSprints(Request $request, IssueRepository $issueRepository, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $sprints = $project->getSprints();
        $burnDownStat = $issueRepository->getBurnDownStat($project);
        return $this->render('sprint/sprint_list.html.twig', [
            'project'=> $project,
            'sprints' => $sprints,
            'burnDownStat' => $burnDownStat,
            'user' => $this->getUser()
        ]);
    }
    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}", name="sprintDetails", methods={"GET"})
     */
    public function viewSprint(Request $request,SprintRepository $sprintRepository,$id_project,$id_sprint): Response
    {   $project = $this->projectRepository->find($id_project);
        $sprint= $sprintRepository->findOneBy([
            'id'=> $id_sprint
        ]);
        $tasks=$sprint->getTasks();

        return $this->render('sprint/sprint_details.html.twig',
            [   'project'=> $project,
                'sprint' => $sprint
            ]);
    }
    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/edit", name="editSprint")
     */
    public function editSprint(Request $request, EntityManagerInterface $entityManager, $id_sprint, $id_project): Response
    {
        $sprint = $this->sprintRepository->find($id_sprint);
        $form = $this->createForm(SprintType::class, $sprint);
        $form->handleRequest($request);
        $project = $this->projectRepository->find($id_project);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($sprint);
                $entityManager->flush();
                $this->notifications->addSuccess("Sprint {$sprint->getNumber()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('sprintsList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('sprint/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/delete", name="deleteSprint")
     */
    public function deleteSprint(Request $request, EntityManagerInterface $entityManager, $id_project, $id_sprint)
    {
        $sprint = $this->sprintRepository->find($id_sprint);
        if (!$sprint) {
            $this->notifications->addError("Aucune sprint n'existe avec l'id {$id_sprint}");
        } else {
            try {
                $entityManager->remove($sprint);
                $entityManager->flush();
                $this->notifications->addSuccess("Sprint {$sprint->getNumber()} supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('sprintsList', [
            'id_project' => $id_project
        ]);
    }
}
