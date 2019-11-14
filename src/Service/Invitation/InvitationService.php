<?php


namespace App\Service\Invitation;


use App\Entity\Invitation;
use App\Entity\Member;
use App\Entity\Project;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Invitation\MemberAlreadyExistsException;
use App\Service\Invitation\MemberIsOwnerException;
use App\Service\Invitation\InvitationAlreadySentException;

class InvitationService
{

    private $invitationRepository;

    private $entityManager;

    public function __construct(InvitationRepository $invitationRepository, EntityManagerInterface $entityManager)
    {
        $this->invitationRepository = $invitationRepository;
        $this->entityManager = $entityManager;
    }

    public function inviteUser(Member $newMember, Project $project): Invitation
    {

        if ($this->entityManager->contains($newMember) ) {
            throw new InvitationAlreadySentException ("Le membre {$newMember->getName()} est déjà invité à ce projet");
        }

        $memberAlreadyExist = $project->getMembers()->contains($newMember);
        
        if ($memberAlreadyExist) {
            throw new MemberAlreadyExistsException("Le membre {$newMember->getName()} est déjà collaborateur à ce projet");
        }

        if($project->getOwner()->getId() ==  $newMember->getId()) {
            throw new MemberIsOwnerException("Vous ne pouvez vous invité à un projet que vous avez crée");
        }
        
        $invitation = new Invitation($newMember, $project);

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        $message = '<html><body><div style="margin-left: 0; margin-right: 0; text-align: center;"><h3>Vous avez été invité à rejoindre le projet "' . $project->getName() . '"</h3></div></body></html>';
        $headers = "From: FireScrum\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";
        $subject = 'Invitation';
        //mail($newMember->getUsername(),$subject,utf8_decode($message),$headers);

        return $invitation;
    }


}
