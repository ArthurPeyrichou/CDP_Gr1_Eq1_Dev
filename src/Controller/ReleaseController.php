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

    public function __construct(ReleaseRepository $releaseRepository, NotificationService $notifications, ProjectRepository $projectRepository)
    {
        $this->releaseRepository = $releaseRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/project/{id_project}/releases/new_release", name = "createRelease")
     */
    public function viewCreationIRelease(Request $request, EntityManagerInterface $entityManager, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);
        
        $form = $this->createForm(ReleaseType::class, [], [
            ReleaseType::PROJECT => $project
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $number = $data['number'];
                $description= $data['description'];
                $date=$data['date'];
                $sprint=$data['sprint'];
                $link=$data['link'];
                $release=new Release($number,$description,$date,$link,$sprint,$project);
                $entityManager->persist($release);
                $entityManager->flush();
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
     * @Route("/project/{id_project}/releases", name="releasesList", methods={"GET"})
     */
    public function viewReleases(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $releases = $project->getReleases();
        return $this->render('release/release_list.html.twig', [
            'project'=> $project,
            'releases' => $releases,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id_project}/releases/{id_release}/edit", name="editRelease")
     */
    public function editRelease(Request $request, EntityManagerInterface $entityManager, $id_release, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);
        $release = $this->releaseRepository->find($id_release);
        $form = $this->createForm(ReleaseType::class, $release, [
            ReleaseType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($release);
                $entityManager->flush();
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
     * @Route("/project/{id_project}/releases/{id_release}/delete", name="deleteRelease")
     */
    public function deleteRelease(Request $request, EntityManagerInterface $entityManager, $id_project, $id_release)
    {
        $release = $this->releaseRepository->find($id_release);
        if (!$release) {
            $this->notifications->addError("Aucune release n'existe avec l'id {$id_release}");
        } else {
            try {
                $entityManager->remove($release);
                $entityManager->flush();
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
