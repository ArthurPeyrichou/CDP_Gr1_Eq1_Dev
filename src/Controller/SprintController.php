<?php
// src/Controller/SprintController.php
namespace App\Controller;

use App\Form\SprintType;
use App\Entity\Sprint;
use App\Repository\SprintRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class SprintController extends AbstractController {

    /**
     * @Route("/project/{id_project}/sprints/new", name="createSprint")
     */
    public function viewCreationSprint(Request $request, ProjectRepository $projectRepository,
                                       EntityManagerInterface $entityManager, SprintRepository $sprintRepository,
                                       $id_project) : Response
    {
        $project = $projectRepository->find($id_project);
        $nextNumber = $sprintRepository->getNextNumber($project);
        $form = $this->createForm(SprintType::class, ['number' => $nextNumber]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null;
            $success = null;
            try {
                $data = $form->getData();
                $description= $data['description'];
                $startDate=$data['startDate'];
                $endDate=$data['endDate'];
                $sprint = new Sprint($project, $nextNumber, $description, $startDate, $endDate);
                $success =  "Sprint {$sprint->getNumber()} créée avec succés.";
                $entityManager->persist($sprint);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }

            return $this->renderSprint($error, $success , $project);
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
    public function viewSprints(Request $request, ProjectRepository $projectRepository, $id_project) {
        return $this->renderSprint(null, null, $projectRepository->find($id_project));
    }

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/edit", name="editSprint")
     */
    public function editSprint(Request $request, EntityManagerInterface $entityManager,
                               ProjectRepository $projectRepository, SprintRepository $sprintRepository,
                               $id_sprint, $id_project): Response
    {
        $sprint = $sprintRepository->find($id_sprint);
        $form = $this->createForm(SprintType::class, $sprint);
        $form->handleRequest($request);
        $project = $projectRepository->find($id_project);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($sprint);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            return $this->renderSprint($error, "Sprint {$sprint->getNumber()} éditée avec succés.", $project);
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
    public function deleteSprint(Request $request, SprintRepository $sprint_Repository,
                                 EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $id_project, $id_sprint)
    {
        $sprint = $sprint_Repository->find($id_sprint);
        $error = null;
        $success = null;
        if (!$sprint) {
            $error ="Aucune sprint n'existe avec l'id {$id_sprint}";
        } else {
            try {
                $success = "Sprint {$sprint->getNumber()} supprimée avec succés.";
                $entityManager->remove($sprint);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return $this->renderSprint($error, $success, $projectRepository->find($id_project));
    }

    private function renderSprint($error, $success, $project) {

        $sprints = $project->getSprints();

        return $this->render('sprint/sprint_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'sprints' => $sprints,
            'user' => $this->getUser()
        ]);
    }
}
