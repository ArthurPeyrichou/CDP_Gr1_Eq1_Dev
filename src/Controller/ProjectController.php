<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Invitation;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\Project\ProjectService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {

    /**
     * @Route("/new_project", name = "newProjectGet")
     */

    public function viewCreationProject(Request $request) : Response
    {
            $form = $this->createForm(ProjectType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $date=new \DateTime('now');
                $project = new Project('1', $name, $description,$date);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->redirectToRoute('dashboard');
            }

            return $this->render('project/creation.html.twig', ["form"=> $form->createView()] );

    }

        /**
         * @Route("/project/{id_project}", name = "projectOverviewGet", methods = {"GET"})
         */
        public function viewProject(Request $request) {
        $pseudo = $this->get('session')->get('pseudo');
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $theProject = $repository->findOneBy([
            'id' => $request->attributes->get('id_project')
        ]);
        $repository = $this->getDoctrine()->getRepository(Member::class);
        $owner = $repository->findOneBy([
            'id' => $theProject->getMANAGERID()
        ]);
        return ($pseudo == null) ? $this->redirect( 'login') : $this->render('project/project_details.html.twig', ["theProject"=> $theProject,
            "owner"=> $owner,
            "members"=> $theProject->getMembers(),
            "pseudo"=> $pseudo]);
    }

}
