<?php


namespace App\Tests\UnitTest\Entity;


use App\Entity\Project;
use App\Entity\Sprint;
use PHPUnit\Framework\TestCase;

class SprintTest extends TestCase
{

    /**
     * @dataProvider getFinishedSprints
     */
    public function testFinished(Sprint $sprint)
    {
        $this->assertTrue($sprint->isFinished());
    }

    /**
     * @dataProvider getNotFinishedSprints
     */
    public function testNotFinished(Sprint $sprint)
    {
        $this->assertFalse($sprint->isFinished());
    }

    /**
     * @dataProvider getEndDateData
     */
    public function testEndDate(Sprint $sprint, \DateTimeInterface $expected)
    {
        $this->assertEquals($sprint->getEndDate(), $expected);
    }


    public function getFinishedSprints(): array
    {
        return [
            [new Sprint($this->getMockProject(), 1, 'Random desc 1', new \DateTimeImmutable('1800-01-01'), 30)],
            [new Sprint($this->getMockProject(), 2, 'Random desc 2', new \DateTimeImmutable('0000-01-01'), 30)],
            [new Sprint($this->getMockProject(), 2, 'Random desc 3', new \DateTimeImmutable('0000-12-31'), 4000)],
        ];
    }

    public function getNotFinishedSprints(): array
    {
        return [
            [new Sprint($this->getMockProject(), 3, 'Random desc 4', new \DateTimeImmutable('today'), 30)],
            [new Sprint($this->getMockProject(), 4, 'Random desc 5', new \DateTimeImmutable('4000-01-01'), 30)],
            [new Sprint($this->getMockProject(), 5, 'Random desc 6', new \DateTimeImmutable('today'), 2)]
        ];
    }

    public function getEndDateData(): array
    {
        return [
            [new Sprint($this->getMockProject(), 3, 'Random desc 7', new \DateTimeImmutable('1800-01-01'), 45),
                new \DateTimeImmutable('1800-02-15')],
            [new Sprint($this->getMockProject(), 4, 'Random desc 8', new \DateTimeImmutable('4000-01-01'), 30),
                new \DateTimeImmutable('4000-01-31')],
            [new Sprint($this->getMockProject(), 5, 'Random desc 9', new \DateTimeImmutable('2019-12-10'), 2),
                new \DateTimeImmutable('2019-12-12')],
            [new Sprint($this->getMockProject(), 5, 'Random desc 10', new \DateTimeImmutable('2020-08-31'), 0),
                new \DateTimeImmutable('2020-08-31')]
        ];
    }

    private function getMockProject(): Project
    {
        return $this->createStub(Project::class);
    }
}
