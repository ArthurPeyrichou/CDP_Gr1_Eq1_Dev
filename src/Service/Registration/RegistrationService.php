<?php


namespace App\Service\Registration;


use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * A service allowing to register a new member into the application.
 */
class RegistrationService
{

    private $passwordEncoder;

    private $memberRepository;

    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, MemberRepository $memberRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->memberRepository = $memberRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new member and persists it.
     * @param string $name The name of the new member.
     * @param string $emailAddress The email address to use for the new member.
     * @param string $plainPassword The password to use for the new member, in plain text.
     * @return Member The newly registered member.
     * @throws EmailAddressInUseException If the provided email address is already used by an existing user.
     * @throws MemberNameInUseException If the provided name is already used by an existing user.
     */
    public function registerUser(string $name, string $emailAddress, string $plainPassword): Member
    {
        $emailUsed = $this->memberRepository->findOneBy(['emailAddress' => $emailAddress]) !== null;
        if ($emailUsed) {
            throw new EmailAddressInUseException("L'adresse mail {$emailAddress} est déjà utilisée par un membre");
        }
        $nameUsed = $this->memberRepository->findOneBy(['name' => $name]) !== null;
        if ($nameUsed) {
            throw new MemberNameInUseException("Le pseudo {$name} est déjà utilisé par un membre");
        }

        $member = new Member($name, $emailAddress, $plainPassword);
        $hashedPassword = $this->passwordEncoder->encodePassword($member, $plainPassword);
        $member->setPassword($hashedPassword);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }


}
