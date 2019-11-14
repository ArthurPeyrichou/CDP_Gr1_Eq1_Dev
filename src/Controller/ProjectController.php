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
    public function editProject(Request $request, EntityManagerInterface $entityManager,ProjectRepository $projectRepository, $id): Response
    {

        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        $error = '';

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

            if($error == ''){
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);


    }

    /**
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request,  EntityManagerInterface $entityManager,$id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
        if (!$project) {
            throw $this->createNotFoundException('aucun projet existe avec cet id '.$id);
        }
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember($projectId, $memberId): Response
    {
        throw new HttpException(500, 'TODO');
    }

}
