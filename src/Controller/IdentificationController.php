<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IdentificationController extends AbstractController{
    
    /**
     * @Route("/register", name = "registerGet", methods = {"GET"})
     */
    public function viewRegister() {
    	return $this->render('member/register.html.twig');
    }

    /**
     * @Route("/register", name = "registerPost", methods = {"POST"})
     * 
     */
    public function registerSubmit() {
    	return $this->render('member/register.html.twig', ["msg"=>"Formulaire envoyé!"]);
    }
}