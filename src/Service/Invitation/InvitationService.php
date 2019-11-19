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

        $invitation = $this->invitationRepository->findOneBy([
            'project' => $project,
            'member' => $newMember
        ]);
        
        if($invitation != null) {
            throw new InvitationAlreadySentException ("Le membre {$newMember->getName()} est déjà invité à ce projet");
        }

        $memberAlreadyExist = $project->getMembers()->contains($newMember);
        
        if ($memberAlreadyExist) {
            throw new MemberAlreadyExistsException("Le membre {$newMember->getName()} est déjà collaborateur de ce projet");
        }

        if($project->getOwner()->getId() ==  $newMember->getId()) {
            throw new MemberIsOwnerException('Vous ne pouvez pas vous inviter à un projet que vous avez créé');
        }
        
        $invitation = new Invitation($newMember, $project);

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $invitation;
    }


}
