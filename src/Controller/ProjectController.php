<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\MEMBER;
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
        $projects = $repository->findAll();
        $pseudo = $this->get('session')->get('pseudo');

    	return ($pseudo == null) ? $this->redirect( 'login') : $this->render('home.html.twig', ["projects"=> $projects, "pseudo"=> $pseudo]);
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

}