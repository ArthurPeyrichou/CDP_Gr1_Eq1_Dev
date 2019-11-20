<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberFixture extends Fixture
{
    public const MEMBER_1 = 'member1';
    public const MEMBER_2 = 'member2';
    public const MEMBER_3 = 'member3';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $randomPass = bin2hex(random_bytes(64));

        $this->createUser($manager, self::MEMBER_1, 'member1@domain.com', $randomPass);
        $this->createUser($manager, self::MEMBER_2, 'member2@domain.com', $randomPass);
        $this->createUser($manager, self::MEMBER_3, 'member3@domain.com', $randomPass);

        $manager->flush();
    }

    private function createUser(ObjectManager $manager, string $name, string $email, string $password): void
    {
        $member = new Member($name, $email, $password);
        $manager->persist($member);
        $this->addReference($name, $member);
    }
}
