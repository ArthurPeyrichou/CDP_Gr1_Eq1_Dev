<?php

namespace App\DataFixtures;

use App\Entity\Issue;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class IssueFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /**@var $project1 Project*/
        $project1 = $this->getReference(ProjectFixture::PROJECT_1);
        /**@var $project2 Project*/
        $project2 = $this->getReference(ProjectFixture::PROJECT_2);

        $project1->addIssue(new Issue(1, 'First test issue', 5, Issue::PRIORITY_HIGH, Issue::TODO, $project1));
        $project1->addIssue(new Issue(2, 'Second test issue', 7, Issue::PRIORITY_LOW, Issue::DOING, $project1));
        $project1->addIssue(new Issue(3, 'Thrid test issue', 2, Issue::PRIORITY_MEDIUM, Issue::DONE, $project1));

        $project2->addIssue(new Issue(1, 'Project2 test issue', 4, Issue::PRIORITY_HIGH, Issue::TODO, $project2));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class
        ];
    }
}
