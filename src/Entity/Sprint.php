<?php

namespace App\Entity;

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
     * @ORM\Column(type="float")
     */
    private $estimated_duration;


    public function __construct($project, $name, $description, $startDate,$estimated_duration)
    {
        $this->project = $project;
        $this->number = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->estimated_duration=$estimated_duration;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

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

    public function getEstimatedDuration(): ?float
    {
        return $this->estimated_duration;
    }

    public function setEstimatedDuration(float $estimated_duration): self
    {
        $this->estimated_duration = $estimated_duration;

        return $this;
    }

}
