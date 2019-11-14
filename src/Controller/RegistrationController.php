<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Form\RegistrationType;
use App\Service\Registration\EmailAddressInUseException;
use App\Service\Registration\MemberNameInUseException;
use App\Service\Registration\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController {

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, RegistrationService $registrationService) : Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        $error = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
           
            $name = $data['name'];
            $emailAddress = $data['emailAddress'];
            $password = $data['password'];

            try {
                $registrationService->registerUser($name, $emailAddress, $password);
                return $this->redirectToRoute('login');
            }
            catch (MemberNameInUseException $e) {
                $error = 'Le nom d\'utilisateur choisi existe déjà';
            }
            catch (EmailAddressInUseException $e) {
                $error = 'L\'adresse email choisie est déjà utilisée';
            }
            if($error == null){
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('member/register.html.twig', ["error"=> $error,
                                                            "form"=> $form->createView()] );
    }

}
