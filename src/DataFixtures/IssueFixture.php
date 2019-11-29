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
        foreach ($this->getIssueData() as [$number, $description, $difficulty, $priority, $status, $projectRef]) {
            $this->loadIssue($manager, $projectRef, $number, $description, $difficulty, $priority, $status);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class
        ];
    }

    private function loadIssue(ObjectManager $manager, string $projectRef, int $number, string $description,
                               int $difficulty, string $priority, string $status): void
    {
        /**@var $project Project*/
        $project = $this->getReference($projectRef);
        $issue = new Issue($number, $description, $difficulty, $priority, $status, $project);
        $project->addIssue($issue);

        $manager->persist($project);
    }

    private function getIssueData(): array
    {
        return [
            // number, description, difficulty, priority, status, projectRef
            [1, 'First test issue', 5, Issue::PRIORITY_HIGH, Issue::TODO, ProjectFixture::PROJECT_1],
            [2, 'Second test issue', 7, Issue::PRIORITY_HIGH, Issue::DOING, ProjectFixture::PROJECT_1],
            [3, 'Third test issue', 2, Issue::PRIORITY_HIGH, Issue::DOING, ProjectFixture::PROJECT_1],
            [4, 'Forth test issue', 1, Issue::PRIORITY_HIGH, Issue::DONE, ProjectFixture::PROJECT_1],
            [1, 'A random test issue', 3, Issue::PRIORITY_HIGH, Issue::TODO, ProjectFixture::PROJECT_2],
            [2, 'Another test issue', 2, Issue::PRIORITY_HIGH, Issue::DONE, ProjectFixture::PROJECT_2],
            [3, 'Some test issue', 7, Issue::PRIORITY_HIGH, Issue::TODO, ProjectFixture::PROJECT_2],
            [4, 'Yet another test issue', 5, Issue::PRIORITY_HIGH, Issue::DOING, ProjectFixture::PROJECT_2],
            [5, 'Again a test issue', 1, Issue::PRIORITY_HIGH, Issue::DONE, ProjectFixture::PROJECT_2],
            [6, 'Sixth test issue', 4, Issue::PRIORITY_HIGH, Issue::DOING, ProjectFixture::PROJECT_2],
        ];
    }
}
