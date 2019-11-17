<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
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

    public function __construct()
    {
        $this->relatedIssues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequiredManDays(): ?float
    {
        return $this->requiredManDays;
    }

    public function setRequiredManDays(float $requiredManDays): self
    {
        $this->requiredManDays = $requiredManDays;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
