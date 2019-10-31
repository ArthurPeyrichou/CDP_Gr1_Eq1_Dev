<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\MEMBER;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IdentificationController extends AbstractController {
    
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
        $member = new MEMBER($request->get('pseudo'), $request->get('email'), $request->get('password') );
        
        return $this->render('member/register.html.twig', ["msg"=>"Formulaire envoyé!"]);
    }

    /**
     * @Route("/login", name = "loginGet", methods = {"GET"})
     */
    public function viewLogin() {
        return $this->render('member/login.html.twig');
    }

    /**
     * @Route("/login", name = "loginPost", methods = {"POST"})
     */
    public function loginSubmit(Request $request) {
        $email = $request->get('email');
        $password = $request->get('password');
        
        $repository = $this->getDoctrine()->getRepository(MEMBER::class);

        $member = $repository->findOneBy([
            'MAIL' => $email,
            'PASSWORD' => $password,
        ]);

        if (!$member) {
            throw $this->createNotFoundException('No member found with this email and password');
        }
        
        return $this->render('member/login.html.twig', ["msg"=> "Hello " . $member->getPSEUDO()]);
    }

}