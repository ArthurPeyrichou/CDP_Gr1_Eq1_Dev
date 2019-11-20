<?php
// src/Controller/ReleaseController.php
namespace App\Controller;

use App\Form\ReleaseType;
use App\Entity\Release;
use App\Entity\Issue;
use App\Entity\Project;
use App\Repository\ReleaseRepository;
use App\Repository\SprintRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;


class ReleaseController extends AbstractController
{

    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/releases/new_release", name = "createRelease")
     */
    public function viewCreationIRelease(Request $request, ProjectRepository $projectRepository,
                                         EntityManagerInterface $entityManager,SprintRepository $SprintRepository,
                                         $id_project, $id_sprint): Response
    {
        $project = $projectRepository->find($id_project);
        $form = $this->createForm(ReleaseType::class);
        $form->handleRequest($request);
        $sprint = $SprintRepository->findOneBy([
            'id' => intval($id_sprint)
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null;
            $success = null;

                $data = $form->getData();
                $number = $data['number'];
                $description= $data['description'];
                $date=$data['date'];
                $link=$data['link'];
                $issues=$project->getIssues();
                $implementedIssues = new ArrayCollection();
                foreach ($issues as $issue)
                {   if ($issue->getStatus()=='DONE')
                    $implementedIssues[]=$issue;
                }
                $release=new Release($number,$description,$date,$link,$implementedIssues,$sprint,$project);

                $success =  "Le release a été créer avec succés.";
                $entityManager->persist($release);
                $entityManager->flush();

            return $this->redirectToRoute('releasesList', [
                'id_project' => $id_project,
                'id_sprint' => $id_sprint

            ]);

        }
        return $this->render('release/release_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }
    /**
     * @Route("/project/{id_project}/sprints/{id_sprint}/releases", name="releasesList", methods={"GET"})
     */
    public function viewReleases(Request $request, ProjectRepository $projectRepository, $id_project,SprintRepository $sprintRepository, $id_sprint) {
        return $this->renderRelease(null, null, $projectRepository->find($id_project),$sprintRepository->find($id_sprint));
    }

    private function renderRelease($error, $success, $project,$sprint) {

        $releases = $project->getReleases();
        return $this->render('release/release_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'sprint' => $sprint,
            'releases' => $releases,
            'user' => $this->getUser()
        ]);
    }


}