<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue
{
    public const TODO = 'todo';
    public const DONE = 'done';
    public const DOING = 'doing';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priority;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sprint", inversedBy="issues")
     */
    private $sprints;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Task", mappedBy="relatedIssues")
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="issues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="issue")
     */
    private $tests;



    public function __construct(int $number, string $description, int $difficulty, string $priority, Project $project, ?Sprint $sprint = null)
    {
        $this->number = $number;
        $this->description = $description;
        $this->difficulty = $difficulty;
        $this->priority = $priority;
        $this->project = $project;
        $this->tasks = new ArrayCollection();
        $this->tests = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        if ($sprint) {
            $this->addSprint($sprint);
        }
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): string
    {
        if (count($this->tasks) == 0) {
            return self::TODO;
        }

        $nbTodos = $this->getTasksNumberByStatus(Task::TODO);
        $nbDoing = $this->getTasksNumberByStatus(Task::DOING);
        $nbDones = $this->getTasksNumberByStatus(Task::DONE);

        $globalStatus = self::TODO;

        if($nbDoing > 0 || $nbDones > 0 && $nbTodos > 0) {
            $globalStatus = self::DOING;
        }
        else if ($nbDones > 0 && $nbTodos == 0) {
            $globalStatus = self::DONE;
        }

        return $globalStatus;
    }

    private function getTasksNumberByStatus(string $status): int
    {
        $count = 0;
        foreach ($this->tasks as $task) {
            $count = $task->getStatus() == $status ? $count + 1 : $count;
        }
        return $count;
    }

    public function getProportionOfDone(): float
    {
        if (count($this->tasks) == 0) {
            return 0;
        }

        return round($this->getTasksNumberByStatus(Task::DONE) / count($this->tasks), 2);
    }

    public function getProportionOfDoing(): float
    {
        if (count($this->tasks) == 0) {
            return 0;
        }

        return round($this->getTasksNumberByStatus(Task::DOING) / count($this->tasks), 2);
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->addRelatedIssue($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            $task->removeRelatedIssue($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sprint[]
     */
    public function getSprints(): Collection
    {
        return $this->sprints;
    }

    public function addSprint(Sprint $sprint): self
    {
        if (!$this->sprints->contains($sprint)) {
            $this->sprints[] = $sprint;
            $sprint->addIssue($this);
        }

        return $this;
    }

    public function removeSprint(Sprint $sprint): self
    {
        if ($this->sprints->contains($sprint)) {
            $this->sprints->removeElement($sprint);
            $sprint->removeIssue($this);
        }

        return $this;
    }

    /**
     * @return Collection|Test[]
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setIssue($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getIssue() === $this) {
                $test->setIssue(null);
            }
        }

        return $this;
    }

}
