<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\InvitationRepository;
use App\Repository\MemberRepository;
use App\Service\RenderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {

    /**
     * @Route("/project/new", name="createProject")
     */
    public function createProject(Request $request, EntityManagerInterface $entityManager,
                                  RenderService $renderService) : Response
    {
        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $owner = $this->getUser();
            $name = $data['name'];
            $description= $data['description'];
            $date= new \DateTime('now');
            $project = new Project($owner, $name, $description, $date);

            $entityManager->persist($project);
            $entityManager->flush();

            if($error == null){
                return $this->render('project/project_details.html.twig',
                    $renderService->renderProjectDetails(
                        $this->getUser(), $error, 'Création du projet réussie', 'owner', $project, null
                    )
                );
            }
        }

        return $this->render('project/creation.html.twig', [
            'error'=> $error,
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);

    }

    /**
     * @Route("/project/{id}", name="projectDetails", methods={"GET"})
     */
    public function viewProject(Request $request, ProjectRepository $projectRepository, InvitationRepository $invitationRepository,
                                RenderService $renderService,  $id): Response
    {
        $project = $projectRepository->findOneBy([
            'id' => intval($id)
        ]);

        $user = $this->getUser();
        $status = null;
        $myInvitation = null;

        if($project->getOwner() == $user){
            $status = 'owner';
        } else if($project->getMembers()->contains($user) ) {
            $status = 'member';
        } else {
            $myInvitation = $invitationRepository->findOneBy([
                'project' => $project,
                'member' => $user
            ]);
            if($myInvitation){
                $status = 'invited';
            } else {
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('project/project_details.html.twig',
            $renderService->renderProjectDetails($this->getUser(), null, null, $status, $project, $myInvitation));
    }

    /**
     * @Route("/project/{id}/edit", name="editProject")
     */
    public function editProject(Request $request, EntityManagerInterface $entityManager,ProjectRepository $projectRepository,
                                RenderService $renderService, $id): Response
    {
        $project =  $projectRepository->find($id);

        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /**@var $owner Member */
            $owner = $this->getUser();
            $name = $data['name'];
            $description= $data['description'];
            $date= new \DateTime();
            $project->setName($name);
            $project->setDescription($description);
            $project->setOwner($owner);
            $project->setCreationDate($date);
            $entityManager->persist($project);
            $entityManager->flush();

            if($error == null){
                return $this->render(
                    'project/project_details.html.twig',
                    $renderService->renderProjectDetails(
                        $this->getUser(), $error, 'Edition du projet réussie', 'owner', $project, null
                    )
                );
            }
        }

        return $this->render('project/edit.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);


    }

    /**
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $entityManager,
                                  RenderService $renderService, $id)
    {
        $project = $projectRepository->find($id);
        $error = null;
        if (!$project) {
            $error = "Aucun projet n'existe avec l'id {$id}";
        }
        else {
            $entityManager->remove($project);
            $entityManager->flush();
        }
        if($error != null){
            return $this->render('project/project_details.html.twig',
                $renderService->renderProjectDetails($this->getUser(), $error, null, "owner", $project, null));
        }

        return $this->render('project/dashboard.html.twig',
            $renderService->renderDashboard($this->getUser(), null, 'Suppression du projet réussie') );
    }

    /**
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember(ProjectRepository $projectRepository, MemberRepository $memberRepository,
                                 RenderService $renderService, EntityManagerInterface $entityManager, $projectId, $memberId): Response
    {

        $error = null;
        $status = null;
        $success = null;
        $project =null;
        $user = $this->getUser();
        $status = null;

        $member = $memberRepository->find($memberId);
        $project = $projectRepository->find($projectId);
        if (!$member) {
            $error = "Aucun membre n'existe avec l'id {$memberId}";
        }
        else if (!$project) {
            $error = "Aucun projet n'existe avec l'id {$projectId}";
        }
        else if($member->getId() != $user->getId()) {
            $error = 'Vous ne pouvez pas supprimer un collaborateur d\'un projet dont vous n\'êtes pas propriétaire';
        }
        else if($project->getOwner() == $user){
            $status = 'owner';
            $success = "{$member->getName()} a été retiré du projet avec succès";
            $project->removeMember($member);
            $entityManager->flush();
        }

        if($error == null && $project->getOwner() != $user) {
            return $this->render('project/dashboard.html.twig',
                $renderService->renderDashboard($this->getUser(), null, "Vous venez de quitter le projet {$project->getName()}"));
        }
        return $this->render('project/project_details.html.twig',
            $renderService->renderProjectDetails($this->getUser(), $error, $success, $status, $project, null));
    }

}
