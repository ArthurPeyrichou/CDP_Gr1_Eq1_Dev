<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\Member;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController {
    
    /**
     * @Route("/register", name = "registerGet", methods = {"GET"})
     */
    public function viewRegister() {
    	return $this->render('member/register.html.twig');
    }

    /**
     * @Route("/register", name = "registerPost", methods = {"POST"})
     */
    public function registerSubmit(Request $request) {
        //On enregistre le nouveau membre en l'ajoutant dans la base de données
        $member = new Member($request->get('pseudo'), $request->get('email'), $request->get('password') );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($member);
        $entityManager->flush();
        
        return $this->render('member/login.html.twig');
    }

}
