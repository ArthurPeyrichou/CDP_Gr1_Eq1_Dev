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

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $commonPass = 'someReallySecurePassword';

        foreach ($this->getUserInfo() as [$email, $reference]) {
            $this->loadUser($manager, $reference, $email, $commonPass);
        }

        $manager->flush();
    }

    private function loadUser(ObjectManager $manager, string $name, string $email, string $password): void
    {
        $member = new Member($name, $email, $password);
        $member->setPassword($this->encoder->encodePassword($member, $member->getPassword()));
        $manager->persist($member);
        $this->addReference($name, $member);
    }

    private function getUserInfo(): array
    {
        return [
            // email, reference
            ['member1@domain.com', self::MEMBER_1],
            ['member2@domain.com', self::MEMBER_2],
            ['member3@domain.com', self::MEMBER_3]
        ];
    }
}
