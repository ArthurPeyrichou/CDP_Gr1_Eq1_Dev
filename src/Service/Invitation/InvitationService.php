<?php


namespace App\Service\Invitation;


use App\Entity\Invitation;
use App\Entity\Member;
use App\Entity\Project;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * A service allowing to invite a member to a project.
 */
class InvitationService
{

    private $invitationRepository;

    private $entityManager;

    public function __construct(InvitationRepository $invitationRepository, EntityManagerInterface $entityManager)
    {
        $this->invitationRepository = $invitationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Creates an invitation for a member to a project, persists it and returns it.
     * @param Member $newMember The member to invite.
     * @param Project $project The project which the member will be invited to.
     * @return Invitation The created invitation.
     * @throws InvitationAlreadySentException If there is already an invitation for that member in the project.
     * @throws MemberAlreadyExistsException If the member is already collaborator in the project.
     * @throws MemberIsOwnerException If the member if the owner of the project.
     */
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
