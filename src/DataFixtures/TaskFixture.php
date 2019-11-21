<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /**@var $project1 Project*/
        $project1 = $this->getReference(ProjectFixture::PROJECT_1);
        /**@var $project2 Project*/
        $project2 = $this->getReference(ProjectFixture::PROJECT_2);

        $project1->addTask(new Task(1, 'A test task', 0.5, [], $project1, null));

        $project2->addTask(new Task(1, 'A second test task', 1, [], $project2, null));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class
        ];
    }
}
