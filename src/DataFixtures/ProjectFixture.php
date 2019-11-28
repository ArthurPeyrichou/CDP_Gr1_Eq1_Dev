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
        foreach ($this->getProjectData() as [$reference, $ownerRef, $name, $description, $date, $contributorRefs]) {
            $this->loadProject($manager, $reference, $ownerRef, $name, $description, $date, $contributorRefs);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MemberFixture::class
        ];
    }

    private function loadProject(ObjectManager $manager, string $reference, string $ownerRef, string $name,
                                 string $description, \DateTimeInterface $date, array $contributorRefs)
    {
        /**@var $owner Member*/
        $owner = $this->getReference($ownerRef);
        $project = new Project($owner, $name, $description, $date);
        foreach ($contributorRefs as $contributorRef) {
            /**@var $contributor Member*/
            $contributor = $this->getReference($contributorRef);
            $project->addMember($contributor);
        }

        $manager->persist($project);
        $this->addReference($reference, $project);
    }

    private function getProjectData(): array
    {
        return [
            // reference, ownerRef, name, description, date, contributorRefs
            [self::PROJECT_1, MemberFixture::MEMBER_1, 'Project 1', 'The first project with a collaborator',
                new \DateTimeImmutable('2019-11-20'), [MemberFixture::MEMBER_2]],
            [self::PROJECT_2, MemberFixture::MEMBER_1, 'Project 2', 'A second project',
                new \DateTimeImmutable('2020-01-01'), []]
        ];
    }
}
