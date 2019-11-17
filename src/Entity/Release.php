<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReleaseRepository")
 */
class Release
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
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Issue", mappedBy="linkedToRelease")
     */
    private $implementedIssues;


    public function __construct()
    {
        $this->implementedIssues = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection|Issue[]
     */
    public function getImplementedIssues(): Collection
    {
        return $this->implementedIssues;
    }

    public function addImplementedIssue(Issue $implementedIssue): self
    {
        if (!$this->implementedIssues->contains($implementedIssue)) {
            $this->implementedIssues[] = $implementedIssue;
            $implementedIssue->setLinkedToRelease($this);
        }

        return $this;
    }

    public function removeImplementedIssue(Issue $implementedIssue): self
    {
        if ($this->implementedIssues->contains($implementedIssue)) {
            $this->implementedIssues->removeElement($implementedIssue);
            // set the owning side to null (unless already changed)
            if ($implementedIssue->getLinkedToRelease() === $this) {
                $implementedIssue->setLinkedToRelease(null);
            }
        }

        return $this;
    }





}