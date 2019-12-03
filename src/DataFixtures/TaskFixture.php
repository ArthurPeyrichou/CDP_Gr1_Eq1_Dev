<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    use FixtureTrait;

    public function load(ObjectManager $manager)
    {
        foreach ($this->getTaskData() as [$number, $description, $requiredManDays,
                 $issuesNumber, $sprintNumber, $developperRef, $projectRef]) {
            $this->loadIssue($manager, $projectRef, $number, $description, $requiredManDays,
                $issuesNumber, $sprintNumber, $developperRef);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MemberFixture::class,
            ProjectFixture::class,
            IssueFixture::class,
            SprintFixture::class
        ];
    }

    private function loadIssue(ObjectManager $manager, string $projectRef, int $number, string $description,
                               float $requiredManDays, array $issuesNumber, int $sprintNumber, string $developperRef): void
    {
        /**@var $project Project*/
        $project = $this->getReference($projectRef);
        /**@var $developper Member*/
        $developper = $this->getReference($developperRef);
        $issues = [];
        foreach ($issuesNumber as $issueNumber) {
            $issues[] = $this->getIssue($project, $issueNumber);
        }
        $sprint = $this->getSprint($project, $sprintNumber);

        $task = new Task($number, $description, $requiredManDays, $issues, $developper, $sprint);

        $manager->persist($task);
    }

    private function getTaskData(): array
    {
        return [
            // number, description, requiredManDays, issuesNumber, sprintNumber, developperRef, projectRef
            [1, 'First test task', 0.2, [1, 2], 1, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [2, 'Second test task', 0.7, [1, 2], 1, MemberFixture::MEMBER_2, ProjectFixture::PROJECT_1],
            [3, 'Third test task', 0.5, [1, 2], 1, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [4, 'Forth test task', 0.1, [1, 2], 1, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [1, 'Fifth test task', 0.2, [3, 4], 2, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [2, 'Sixth test task', 0.7, [3, 4], 2, MemberFixture::MEMBER_2, ProjectFixture::PROJECT_1],
            [3, 'Seventh test task', 0.5, [3, 4], 2, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [4, 'Eighth test task', 0.1, [3, 4], 2, MemberFixture::MEMBER_1, ProjectFixture::PROJECT_1],
            [1, 'Nineth test task', 0.2, [1], 1, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [2, 'Tenth test task', 0.7, [6], 1, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [3, 'Eleventh test task', 0.5, [1, 6], 1, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [4, 'Twelfth test task', 0.1, [6], 1, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [1, 'Thirteenth test task', 0.2, [2, 3, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [2, 'Forteenth test task', 0.7, [3, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [3, 'Fifteenth test task', 0.5, [2, 3, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [4, 'Sixteenth test task', 0.1, [2, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [5, 'Seventeenth test task', 0.2, [2, 3], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [6, 'Eigthteenth test task', 0.7, [2], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [7, 'Nineteenth test task', 0.5, [3, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
            [8, 'Twentieth test task', 0.1, [2, 4], 2, MemberFixture::MEMBER_3, ProjectFixture::PROJECT_2],
        ];
    }

}
