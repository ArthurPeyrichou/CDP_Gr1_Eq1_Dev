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
     * @Route("/home", name = "homeGet", methods = {"GET"})
     */
    public function viewHome(Request $request) {

        $repository = $this->getDoctrine()->getRepository(PROJECT::class);
        $myProjects = $repository->findBy([
            'MANAGER_ID' => $this->get('session')->get('id')
        ]);
        //Remplacer la requette pour selectionner les projet lié et non les projet enfants
        $myLinkedProjects = $repository->findBy([
            'MANAGER_ID' => $this->get('session')->get('id')
        ]);
        $pseudo = $this->get('session')->get('pseudo');

        return ($pseudo == null) ? $this->redirect( 'login') : $this->render('home.html.twig', ["myProjects"=> $myProjects,
                                                                                                "myLinkedProjects"=> $myLinkedProjects, 
                                                                                                "pseudo"=> $pseudo]);
    }

    /**
     * @Route("/new_project", name = "newProjectGet", methods = {"GET"})
     */
    public function viewCreationProject(Request $request) {
    	$pseudo = $this->get('session')->get('pseudo');
    	return ($pseudo == null) ? $this->redirect( 'login') : $this->render('project/creation.html.twig', ["pseudo"=> $pseudo]);
    }

    /**
     * @Route("/new_project", name = "newProjectPost", methods = {"POST"})
     */
    public function creationProjectSubmit(Request $request) {
        //On enregistre le nouveau projet en l'ajoutant dans la base de données
        //Attention le 1 en premier parametre dois etre remplacer par l'id de du membre que l'on passera a l'avenir en parametre session
        $project = new PROJECT(1, $request->get('title'), $request->get('desc') );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();
        
        return $this->redirect( 'home');
    }

    /**
     * @Route("/project/{id_project}", name = "projectOverviewGet", methods = {"GET"})
     */
    public function viewProject(Request $request) {
        $pseudo = $this->get('session')->get('pseudo');
        $repository = $this->getDoctrine()->getRepository(PROJECT::class);
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
