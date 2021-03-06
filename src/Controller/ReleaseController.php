<?php
// src/Controller/ReleaseController.php
namespace App\Controller;

use App\Form\ReleaseType;
use App\Entity\Release;
use App\Entity\Issue;
use App\Entity\Project;
use App\Repository\ReleaseRepository;
use App\Repository\SprintRepository;
use App\Service\NotificationService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;


class ReleaseController extends AbstractController
{

    private $releaseRepository;
    private $notifications;
    private $projectRepository;
    private $entityManager;

    public function __construct(ReleaseRepository $releaseRepository, NotificationService $notifications, ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        $this->releaseRepository = $releaseRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Displays and handles the release creation form.
     * @Route("/project/{id_project}/releases/new_release", name = "createRelease")
     */
    public function viewCreationRelease(Request $request, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);

        $form = $this->createForm(ReleaseType::class, [], [
            ReleaseType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $release=new Release($data['number'], $data['description'], $data['date'], $data['link'], $data['sprint'],$project);
                $this->entityManager->persist($release);
                $this->entityManager->flush();
                $this->notifications->addSuccess("La release a été créer avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            
            return $this->redirectToRoute('releasesList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('release/release_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * Displays the release list page.
     * @Route("/project/{id_project}/releases", name="releasesList", methods={"GET"})
     */
    public function viewReleases(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);

        return $this->render('release/release_list.html.twig', [
            'project'=> $project,
            'releases' => $project->getReleases(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * Displays and handles the release edit form.
     * @Route("/project/{id_project}/releases/{id_release}/edit", name="editRelease")
     */
    public function editRelease(Request $request, $id_release, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);
        $release = $this->releaseRepository->find($id_release);
       
        $form = $this->createForm(ReleaseType::class, $release, [
            ReleaseType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($release);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Release {$release->getNumber()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('releasesList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('release/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * Handles the deletion of a release.
     * @Route("/project/{id_project}/releases/{id_release}/delete", name="deleteRelease")
     */
    public function deleteRelease(Request $request, $id_project, $id_release)
    {
        $release = $this->releaseRepository->find($id_release);
        if (!$release) {
            $this->notifications->addError("Aucune release n'existe avec l'id {$id_release}");
        } else {
            try {
                $this->entityManager->remove($release);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Release {$release->getNumber()} supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('releasesList', [
            'id_project' => $id_project
        ]);
    }

}
