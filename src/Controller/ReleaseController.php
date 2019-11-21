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
     * @Route("/project/{id_project}/releases/new_release", name = "createRelease")
     */
    public function viewCreationIRelease(Request $request, ProjectRepository $projectRepository,
                                         EntityManagerInterface $entityManager,
                                         $id_project): Response
    {
        $project = $projectRepository->find($id_project);
        $form = $this->createForm(ReleaseType::class);
        $form->handleRequest($request);

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
                $release=new Release($number,$description,$date,$link,$implementedIssues,$project);

                $success =  "Le release a été créer avec succés.";
                $entityManager->persist($release);
                $entityManager->flush();

            return $this->renderRelease($error, $success,$project);
        }
        return $this->render('release/release_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }
    /**
     * @Route("/project/{id_project}/releases", name="releasesList", methods={"GET"})
     */
    public function viewReleases(Request $request, ProjectRepository $projectRepository, $id_project) {
        return $this->renderRelease(null, null, $projectRepository->find($id_project));
    }

    /**
     * @Route("/project/{id_project}/releases/{id_release}/edit", name="editRelease")
     */
    public function editRelease(Request $request, EntityManagerInterface $entityManager,
                                ProjectRepository $projectRepository, ReleaseRepository $releaseRepository,
                                $id_release, $id_project): Response
    {
        $release = $releaseRepository->find($id_release);
        $form = $this->createForm(ReleaseType::class, $release);
        $form->handleRequest($request);
        $project = $projectRepository->find($id_project);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($release);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            return $this->renderRelease($error, "Release {$release->getNumber()} éditée avec succés.", $project);
        }

        return $this->render('release/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }
    /**
     * @Route("/project/{id_project}/releases/{id_release}/delete", name="deleteRelease")
     */
    public function deleteRelease(Request $request, ReleaseRepository $release_Repository,
                                  EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $id_project, $id_release)
    {
        $release = $release_Repository->find($id_release);
        $error = null;
        $success = null;
        if (!$release) {
            $error ="Aucune release n'existe avec l'id {$id_release}";
        } else {
            try {
                $success = "Release {$release->getNumber()} supprimée avec succés.";
                $entityManager->remove($release);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return $this->renderRelease($error, $success, $projectRepository->find($id_project));
    }
    private function renderRelease($error, $success, $project) {

        $releases = $project->getReleases();
        return $this->render('release/release_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'releases' => $releases,
            'user' => $this->getUser()
        ]);

    }


}