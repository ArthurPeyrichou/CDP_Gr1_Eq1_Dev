<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Form\RegistrationType;
use App\Service\NotificationService;
use App\Service\Registration\EmailAddressInUseException;
use App\Service\Registration\MemberNameInUseException;
use App\Service\Registration\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController {

    /**
     * Displays and handles the member registration form.
     * @Route("/register", name="register")
     */
    public function register(Request $request, RegistrationService $registrationService, NotificationService $notifications) : Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $registrationService->registerUser($data['name'], $data['emailAddress'], $data['password']);
                $notifications->addSuccess('Votre compte a été créé, vous pouvez vous connecter!');
                return $this->redirectToRoute('login');
            }
            catch (MemberNameInUseException $e) {
                $notifications->addError($e->getMessage());
            }
            catch (EmailAddressInUseException $e) {
                $notifications->addError($e->getMessage());
            }
        }

        return $this->render('member/register.html.twig', ["form"=> $form->createView()] );
    }

}
