<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\Member;
use App\Service\Registration\EmailAddressInUseException;
use App\Service\Registration\MemberNameInUseException;
use App\Service\Registration\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController {

    //FIXME Supprimer les deux premières routes quand le formulaire sera prêt.
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

    /**
     * @Route("/register", name="register")
     */
    public function register(RegistrationService $registrationService) : Response
    {
        $error = '';
        //TODO Récupérer les infos depuis le formulaire quand il sera prêt
        if (false) {
            $name = '';
            $emailAddress = '';
            $password = '';
            try {
                $registrationService->registerUser($name, $emailAddress, $password);
                return $this->redirectToRoute('loginPost');
            }
            catch (MemberNameInUseException $e) {
                $error = 'Le nom d\'utilisateur choisi existe déjà';
            }
            catch (EmailAddressInUseException $e) {
                $error = 'L\'adresse email choisie est déjà utilisée';
            }
        }

        return $this->render('member/register.html.twig');
    }

}
