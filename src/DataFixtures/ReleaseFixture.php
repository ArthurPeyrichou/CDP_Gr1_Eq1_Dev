<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Release;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ReleaseFixture extends Fixture implements DependentFixtureInterface
{
    use FixtureTrait;

    public function load(ObjectManager $manager)
    {
        $data = $this->getReleaseData();
        foreach ($data as [$number, $description, $date, $link, $sprintNumber, $projectRef]) {
            $this->loadRelease($manager, $projectRef, $number, $description, $date, $link, $sprintNumber);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class,
            SprintFixture::class
        ];
    }

    private function loadRelease(ObjectManager $manager, string $projectRef, int $number, string $description,
                                \DateTimeInterface $date, string $link, int $sprintNumber): void
    {
        /**@var $project Project*/
        $project = $this->getReference($projectRef);
        $sprint = $this->getSprint($project, $sprintNumber);
        $release = new Release($number, $description, $date, $link, $sprint, $project);
        $project->addRelease($release);

        $manager->persist($project);
    }

    private function getReleaseData(): array
    {
        return [
            // number, description, date, link, sprintNumber, projectRef
            [1, 'Test release 1 project 1', new \DateTimeImmutable('2019-01-01'), 14, 1, ProjectFixture::PROJECT_1],
            [1, 'Test release 1 project 2', new \DateTimeImmutable('2019-01-15'), 21, 1, ProjectFixture::PROJECT_2],
            [2, 'Test release 2 project 2', new \DateTimeImmutable('2019-10-21'), 21, 2, ProjectFixture::PROJECT_2]
        ];
    }
}
