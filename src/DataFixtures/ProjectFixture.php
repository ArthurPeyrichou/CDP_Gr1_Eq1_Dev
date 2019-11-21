<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_1 = 'project1';
    public const PROJECT_2 = 'project2';

    public function load(ObjectManager $manager)
    {
        /**@var $member1 Member*/
        $member1 = $this->getReference(MemberFixture::MEMBER_1);
        /**@var $member2 Member*/
        $member2 = $this->getReference(MemberFixture::MEMBER_2);
        /**@var $member3 Member*/
        $member3 = $this->getReference(MemberFixture::MEMBER_3);

        $project1 = new Project($member1, 'Project 1', 'The first project', new \DateTimeImmutable('2019-11-20'));
        $manager->persist($project1);
        $project2 = new Project($member2, 'Project 2', 'A project with a collaborator', new \DateTimeImmutable('2020-01-01'));
        $project2->addMember($member3);
        $manager->persist($project2);

        $this->setReference(self::PROJECT_1, $project1);
        $this->setReference(self::PROJECT_2, $project2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MemberFixture::class
        ];
    }
}
