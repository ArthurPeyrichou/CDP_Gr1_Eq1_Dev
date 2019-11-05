<?php

namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
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

        $repository = $this->getDoctrine()->getRepository(Member::class);

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
