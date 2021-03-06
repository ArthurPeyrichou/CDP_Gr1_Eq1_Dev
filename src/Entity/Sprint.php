<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SprintRepository")
 */
class Sprint
{
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
     * @ORM\Column(type="integer")
     */
    private $theoricDoneDiff;

    /**
     * @ORM\Column(type="integer")
     */
    private $doneDiff;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="sprints")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Release", inversedBy="sprint")
     */
    private $release;

    /**
     * @ORM\Column(type="integer")
     */
    private $durationInDays;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="sprint")
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Issue", mappedBy="sprints")
     */
    private $issues;


    public function __construct(Project $project, int $number, string $description, \DateTimeInterface $startDate,
                                int $durationInDays)
    {
        $this->project = $project;
        $this->number = $number;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->durationInDays = $durationInDays;
        $this->tasks = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->theoricDoneDiff = -1;
        $this->doneDiff = -1;
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

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function isFinished(): bool
    {
        $today = new \DateTimeImmutable('today');
        return $today > $this->getEndDate();
    }

    public function getEndDate(): \DateTimeInterface
    {
        return (new \DateTimeImmutable($this->startDate->format('Y-m-d')))->modify("+ {$this->durationInDays} day");
    }

    public function setBurnDownChart(): self
    {
        if($this->isFinished()){
            if( $this->doneDiff < 0 ) {
                $doneDiff = 0;
                $theoricDoneDiff = 0;
                foreach($this->issues as $issue) {
                    $theoricDoneDiff += $issue->getDifficulty();
                    if($issue->getStatus() == Issue::DONE) {
                        $doneDiff += $issue->getDifficulty();
                    }
                }
                $this->doneDiff = $doneDiff;
                $this->theoricDoneDiff = $theoricDoneDiff;
            }
        } else {
            if( $this->doneDiff >= 0 ) {
                $this->doneDiff = -1;
                $this->theoricDoneDiff = -1;
            }
        }

        return $this;
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

    public function getRelease(): ?Release
    {
        return $this->release;
    }

    public function setRelease(?Release $release): self
    {
        $this->release = $release;

        return $this;
    }

    public function getDurationInDays(): int
    {
        return $this->durationInDays;
    }

    public function setDurationInDays(int $durationInDays): self
    {
        $this->durationInDays = $durationInDays;

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
            $task->setSprint($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getSprint() === $this) {
                $task->setSprint(null);
            }
        }

        return $this;
    }

    public function containsNotDoneTask(): bool
    {
        foreach($this->tasks as $task){
            if($task->getStatus() != Task::DONE){
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|Issue[]
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): self
    {
        if (!$this->issues->contains($issue)) {
            $this->issues[] = $issue;
            $issue->addSprint($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): self
    {
        if ($this->issues->contains($issue)) {
            $this->issues->removeElement($issue);
            $issue->removeSprint($this);
        }

        return $this;
    }

}
