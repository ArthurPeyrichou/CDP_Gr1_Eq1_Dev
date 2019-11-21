<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class MemberFixture extends Fixture
{
    public const MEMBER_1 = 'member1';
    public const MEMBER_2 = 'member2';
    public const MEMBER_3 = 'member3';

    public function load(ObjectManager $manager)
    {
        $password = 'someReallySecurePassword';

        $this->createUser($manager, self::MEMBER_1, 'member1@domain.com', $password);
        $this->createUser($manager, self::MEMBER_2, 'member2@domain.com', $password);
        $this->createUser($manager, self::MEMBER_3, 'member3@domain.com', $password);

        $manager->flush();
    }

    private function createUser(ObjectManager $manager, string $name, string $email, string $password): void
    {
        $member = new Member($name, $email, $password);
        $manager->persist($member);
        $this->addReference($name, $member);
    }
}
