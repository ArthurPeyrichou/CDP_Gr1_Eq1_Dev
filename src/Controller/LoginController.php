<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginController extends AbstractController
{
    private $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }


    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, NotificationService $notifications): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();
        if($error){
            $notifications->addError($error->getMessageKey());
        }

        return $this->render('member/login.html.twig', [
            'last_typed_email' => $lastEmail,
        ]);
    }

    /**
     * @Route("/login/forgottenPassword", name="forgottenPassword")
     */
    public function forgottenPassword(Request $request,EntityManagerInterface $entityManager,
                                       Swift_Mailer $mailer, MemberRepository $memberRepository,
                                       TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {

            $email = $request->request->get('emailAddress');

            $member = $memberRepository->findOneBy(['emailAddress'=>$email]);

            if ($member == null) {
                $this->notifications->addError('Cette adresse email n\'existe pas');
            }
            else {
                $token = $tokenGenerator->generateToken();
                $member->setResetToken($token);
                $entityManager->flush();
                $url = $this->generateUrl('resetPassword', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new \Swift_Message('Mot de passe oublié'))
                    ->setFrom(array('firescrum2019@gmail.com' => 'EquipeFirescrum'))
                    ->setTo($member->getEmailAddress())
                    ->setBody(
                        "Réinitialiser votre mot de passe : <a href=\"{$url}\">ici</a>",
                        'text/html'
                    );
                $mailer->send($message);

                $this->notifications->addSuccess('Mail envoyé');
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('member/forgotten_password.html.twig');
    }


    /**
     * @Route("/login/resetPassword/{token}", name="resetPassword")
     */
    public function resetPassword(Request $request, string $token, EntityManagerInterface $entityManager,
                                  MemberRepository $memberRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod('POST')) {

            $member = $memberRepository->findOneBy(['resetToken' => $token]);

            if ($member === null) {
                $this->notifications->addError('Token inconnu');
            }
            else {
                $member->setPassword($passwordEncoder->encodePassword($member, $request->request->get('password')));
                $member->setResetToken('');
                $entityManager->flush();
                $this->notifications->addSuccess('Mot de passe mis à jour');
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('member/reset_password.html.twig', ['token' => $token]);

    }

}
