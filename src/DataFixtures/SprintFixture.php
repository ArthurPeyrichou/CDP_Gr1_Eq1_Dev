<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Sprint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SprintFixture extends Fixture implements DependentFixtureInterface
{
    use FixtureTrait;

    public function load(ObjectManager $manager)
    {
        foreach ($this->getSprintData() as [$number, $description, $startDate, $duration, $issuesNumber, $projectRef]) {
            $this->loadSprint($manager, $projectRef, $number, $description, $startDate, $duration, $issuesNumber);
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

    private function loadSprint(ObjectManager $manager, string $projectRef, int $number, string $description,
                                \DateTimeInterface $startDate, int $duration, array $issuesNumber): void
    {
        /**@var $project Project*/
        $project = $this->getReference($projectRef);
        $sprint = new Sprint($project, $number, $description, $startDate, $duration);
        foreach ($issuesNumber as $issueNumber) {
            $sprint->addIssue($this->getIssue($project, $issueNumber));
        }
        $project->addSprint($sprint);

        $manager->persist($project);
    }

    private function getSprintData(): array
    {
        return [
            // number, description, startDate, duration, issuesNumber, projectRef
            [1, 'First test sprint', new \DateTimeImmutable('2019-01-01'), 14, [1, 2], ProjectFixture::PROJECT_1],
            [2, 'Second test sprint', new \DateTimeImmutable('2019-01-15'), 14, [3, 4], ProjectFixture::PROJECT_1],
            [1, 'Yet another sprint', new \DateTimeImmutable('2019-10-21'), 21, [1, 6], ProjectFixture::PROJECT_2],
            [2, 'Forth test sprint', new \DateTimeImmutable('2019-11-12'), 21, [2, 3, 4], ProjectFixture::PROJECT_2]
        ];
    }
}
