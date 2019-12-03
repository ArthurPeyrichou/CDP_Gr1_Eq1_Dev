<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TestFixture extends Fixture implements DependentFixtureInterface
{
    use FixtureTrait;

    public function load(ObjectManager $manager)
    {
        foreach ($this->getTestData() as [$name, $description, $state, $issueNumber, $projectRef]) {
            $this->loadTest($manager, $projectRef, $name, $description, $state, $issueNumber);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class,
            IssueFixture::class
        ];
    }

    private function loadTest(ObjectManager $manager, string $projectRef, string $name, string $description,
                                 string $state, int $issueNumber): void
    {
        /**@var $project Project */
        $project = $this->getReference($projectRef);
        $issue = $this->getIssue($project, $issueNumber);
        $test = new Test($project, $name, $description, $state, $issue);
        $project->addTest($test);

        $manager->persist($project);
    }

    private function getTestData(): array
    {
        return [
            // name, description, state, issueNumber, projectRef
            ['Test11', 'A random test', Test::TODO, 1, ProjectFixture::PROJECT_1],
            ['Test12', 'A second random test', Test::SUCCEEDED, 2, ProjectFixture::PROJECT_1],
            ['Test13', 'A third random test', Test::FAILED, 4, ProjectFixture::PROJECT_1],
            ['Test21', 'Another random test', Test::TODO, 1, ProjectFixture::PROJECT_2],
            ['Test22', 'Yet another random test', Test::FAILED, 2, ProjectFixture::PROJECT_2],
            ['Test23', 'Still another random test', Test::TODO, 3, ProjectFixture::PROJECT_2],
            ['Test24', 'A random test again', Test::SUCCEEDED, 4, ProjectFixture::PROJECT_2],
            ['Test25', 'This is another random test', Test::SUCCEEDED, 5, ProjectFixture::PROJECT_2],
            ['Test26', 'A final random test', Test::SUCCEEDED, 1, ProjectFixture::PROJECT_2]
        ];
    }
}
