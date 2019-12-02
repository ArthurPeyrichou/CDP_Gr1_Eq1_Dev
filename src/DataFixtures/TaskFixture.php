<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

    }

    public function getDependencies()
    {
        return [
            ProjectFixture::class
        ];
    }
}
