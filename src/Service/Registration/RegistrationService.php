<?php


namespace App\Service\Registration;


use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
