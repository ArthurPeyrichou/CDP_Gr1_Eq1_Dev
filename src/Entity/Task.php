<?php

namespace App\Entity;

use App\EntityException\InvalidStatusTransitionException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    public const TODO = 'todo';
    public const DOING = 'doing';
    public const DONE = 'done';


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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $requiredManDays;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member")
     */
    private $developper;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Issue")
     */
    private $relatedIssues;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sprint", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sprint;

    public function __construct(int $number, string $description, float $requiredManDays,
                                array $relatedIssues, Project $project, ?Member $developper,Sprint $sprint)
    {
        $this->number = $number;
        $this->description = $description;
        $this->requiredManDays = $requiredManDays;
        $this->developper = $developper;
        $this->project = $project;
        $this->relatedIssues = new ArrayCollection($relatedIssues);
        $this->status = self::TODO;
        $this->sprint=$sprint;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
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

    public function getRequiredManDays(): float
    {
        return $this->requiredManDays;
    }

    public function setRequiredManDays(float $requiredManDays): self
    {
        $this->requiredManDays = $requiredManDays;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function begin(): void {
        if ($this->status == self::DONE || $this->status == self::DOING) {
            throw new InvalidStatusTransitionException(
                "Cannot begin a task that has status {$this->getStatus()}"
            );
        }
        $this->status = self::DOING;
    }

    public function finish(): void {
        if ($this->status == self::DONE) {
            throw new InvalidStatusTransitionException(
                "Cannot finish a task that has status {$this->getStatus()}"
            );
        }
        $this->status = self::DONE;
    }

    public function getDevelopper(): ?Member
    {
        return $this->developper;
    }

    public function setDevelopper(?Member $developper): self
    {
        $this->developper = $developper;

        return $this;
    }

    /**
     * @return Collection|Issue[]
     */
    public function getRelatedIssues(): Collection
    {
        return $this->relatedIssues;
    }

    public function addRelatedIssue(Issue $relatedIssue): self
    {
        if (!$this->relatedIssues->contains($relatedIssue)) {
            $this->relatedIssues[] = $relatedIssue;
        }

        return $this;
    }

    public function removeRelatedIssue(Issue $relatedIssue): self
    {
        if ($this->relatedIssues->contains($relatedIssue)) {
            $this->relatedIssues->removeElement($relatedIssue);
        }

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }
}
