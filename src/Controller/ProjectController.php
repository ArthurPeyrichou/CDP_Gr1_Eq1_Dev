<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\INVITATION;
use App\Entity\PROJECT;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {
    
    /**
     * @Route("/dashboard", name = "dashboard", methods = {"GET"})
     */
    public function viewDashboard(Request $request) {
        $member = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(PROJECT::class);
        $myProjects = $repository->findBy([
            'MANAGER_ID' => $member->getId()
        ]);
        //Remplacer la requette pour selectionner les projet lié et non les projet enfants
        $myLinkedProjects = $repository->findBy([
            'MANAGER_ID' => $member->getId()
        ]);
        $pseudo = $member->getName();

        return $this->render('project/dashboard.html.twig', ["myProjects"=> $myProjects,
                                                            "myLinkedProjects"=> $myLinkedProjects, 
                                                            "pseudo"=> $pseudo]);
    }

    /**
     * @Route("/new_project", name = "newProjectGet", methods = {"GET"})
     */
    public function viewCreationProject(Request $request) {
    	$member = $this->getUser();
        $pseudo = $member->getName();
    	return $this->render('project/creation.html.twig', ["pseudo"=> $pseudo]);
    }

    /**
     * @Route("/new_project", name = "newProjectPost", methods = {"POST"})
     */
    public function creationProjectSubmit(Request $request) {
        //On enregistre le nouveau projet en l'ajoutant dans la base de données
        $member = $this->getUser();
        $project = new PROJECT($member->getId(), $request->get('title'), $request->get('desc') );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();
        
        return $this->redirectToRoute( 'dashboard');
    }

    /**
     * @Route("/project/{id_project}", name = "projectOverviewGet", methods = {"GET"})
     */
    public function viewProject(Request $request) {
        $member = $this->getUser();
        $pseudo = $member->getName();
        $repository = $this->getDoctrine()->getRepository(PROJECT::class);
        $theProject = $repository->findOneBy([
            'id' => $request->attributes->get('id_project')
        ]);
        $repository = $this->getDoctrine()->getRepository(Member::class);
        $owner = $repository->findOneBy([
            'id' => $theProject->getMANAGERID()
        ]);
        return $this->render('project/project_details.html.twig', ["theProject"=> $theProject,
                                                                    "owner"=> $owner,
                                                                    "members"=> $theProject->getMembers(),
                                                                    "pseudo"=> $pseudo]);
    }

}
