<?php
// src/Controller/IdentificationController.php
namespace App\Controller;

use App\Entity\Invitation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    /**
     * @Route("/invit", name="create_invitation", methods = {"GET"})
     */
    public function createInvitation() {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        //$invitation = new Invitation(MEMBER_iD, PROJECT_iD);
        //$entityManager->persist($invitation);
        //$entityManager->flush();
        //return $this->render('member/invitation.html.twig', ["msg"=>'Saved new invitation with id '.$invitation->getId()]);
        
        return $this->render('member/invitation.html.twig');
    }
}