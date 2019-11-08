<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
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

        $error = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $owner = $this->getUser();
            $name = $data['name'];
            $description= $data['description'];
            $date= new \DateTime('now');
            $project = new Project($owner, $name, $description, $date);

            $entityManager->persist($project);
            $entityManager->flush();

            if($error == ''){
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('project/creation.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);

    }

    /**
     * @Route("/project/{id}", name="projectDetails", methods={"GET"})
     */
    public function viewProject(Request $request, ProjectRepository $projectRepository, $id): Response
    {
        $theProject = $projectRepository->findOneBy([
            'id' => intval($id)
        ]);
        $owner = $theProject->getOwner();
        return $this->render('project/project_details.html.twig', [
            'project' => $theProject,
            'owner' => $owner,
            'members' => $theProject->getMembers(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id}/edit", name="editProject")
     */
    public function editProject(Request $request, ProjectRepository $projectRepository, $id): Response
    {
        throw new HttpException(500, 'TODO');
    }

    /**
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request, $id)
    {
        throw new HttpException(500, 'TODO');
    }

    /**
     * @Route("/project/{id}/sendInvitation", name="inviteToProject", methods={"POST"})
     */
    public function sendInvitationToProject(Request $request, $id)
    {
        throw new HttpException(500, 'TODO');
    }


    /**
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember($projectId, $memberId): Response
    {
        throw new HttpException(500, 'TODO');
    }

}
