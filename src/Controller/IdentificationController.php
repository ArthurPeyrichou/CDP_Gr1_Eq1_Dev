<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\MEMBER;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($member);
        $entityManager->flush();
        
        return $this->render('member/login.html.twig');
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

        if($member) {
            $this->get('session')->start();

            // set and get session attributes
            $this->get('session')->set('pseudo', $member->getPSEUDO());
            $this->get('session')->set('id', $member->getId());
            $this->get('session')->set('mail', $email);
        }

        return (!$member) ? $this->render('member/login.html.twig', ["msg"=> "No member found with this email and password"]) : $this->redirect( 'home' );
    }

    /**
     * @Route("/logout", name = "logoutGet", methods = {"GET"})
     */
    public function viewLogout() {
        $this->get('session')->clear();
        return $this->redirect( 'login');
    }

}