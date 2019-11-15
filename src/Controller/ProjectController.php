<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\InvitationRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {

    /**
     * @Route("/project/new", name="createProject")
     */
    public function createProject(Request $request, EntityManagerInterface $entityManager) : Response
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
                return $this->redirectToRoute('dashboard');
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
    public function viewProject(Request $request, ProjectRepository $projectRepository, InvitationRepository $invitationRepository, $id): Response
    {
        $theProject = $projectRepository->findOneBy([
            'id' => intval($id)
        ]);
        $owner = $theProject->getOwner();
        $user = $this->getUser();
        $status = null;
        $myInvitation = null;

        if($owner == $user){
            $status = "owner";
        } else if($theProject->getMembers()->contains($user) ) {
            $status = "member";
        } else {
            $myInvitation = $invitationRepository->findOneBy([
                'project' => $theProject,
                'member' => $user
            ]);
            if($myInvitation){
                $status = "invited";
            }
        }

        return $this->render('project/project_details.html.twig', [
            'status' => $status,
            'myInvitation' => $myInvitation,
            'project' => $theProject,
            'owner' => $owner,
            'members' => $theProject->getMembers(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id}/edit", name="editProject")
     */
    public function editProject(Request $request, EntityManagerInterface $entityManager,ProjectRepository $projectRepository, $id): Response
    {

        $project =  $projectRepository->find($id);

        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $owner = $this->getUser();
            $name = $data['name'];
            $description= $data['description'];
            $date= new \DateTime('now');
            $project->setName($name);
            $project->setDescription($description);
            $project->setOwner($owner);
            $project->setCreationDate($date);
            $entityManager->persist($project);
            $entityManager->flush();

            if($error == null){
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('project/edit.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);


    }

    /**
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $entityManager,$id)
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            throw $this->createNotFoundException('aucun projet existe avec cet identifiant '.$id);
        }
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember($projectId, $memberId, ProjectRepository $projectRepository, MemberRepository $memberRepository, EntityManagerInterface $entityManager): Response
    {

        $error = null;
        $status = null;
        $success = null;
        $theProject =null;
        $owner = null;
        try {
            $status = null;

            $theProject = $projectRepository->find($projectId);
            if (!$theProject) {
                throw $this->createNotFoundException('aucun projet existe avec cet identifiant '.$id);
            }
            $owner = $theProject->getOwner();
            if($owner == $this->getUser()){
                $status = "owner";
            } else {
                throw new \RuntimeException("Vous ne pouvez pas supprimer un projet dont vous n'êtes pas propriétaire");
            }

            $member = $memberRepository->find($memberId);
            if (!$member) {
                throw $this->createNotFoundException('aucun membre existe avec cet identifiant '.$id);
            }
            $theProject->removeMember($member);
            $entityManager->flush();
            $success = $member->getName() . " a été retiré du projet avec succés";
        } catch(\Exception $e) {
            $error = $e->getMessage();
        }
        
        
        return $this->render('project/project_details.html.twig', [
            'error' => $error,
            'success' => $success,
            'status' => $status,
            'myInvitation' => null,
            'project' => $theProject,
            'owner' => $owner,
            'members' => $theProject->getMembers(),
            'user' => $this->getUser()
        ]);
    }

}
