<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */

    private $name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $CreationDate;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="App\Entity\MEMBER", inversedBy="ownedProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MEMBER", mappedBy="contributedProjects")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Issue", mappedBy="project")
     */
    private $issues;

    public function __construct($owner, $name, $description,$CreationDate)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->description = $description;
        $this->members = new ArrayCollection();
        $this->CreationDate=$CreationDate;
        $this->issues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->DESCRIPTION;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->CreationDate;
    }

    public function setCreationDate(\DateTimeInterface $CreationDate): self
    {
        $this->CreationDate = $CreationDate;

        return $this;
    }

    /**
     * @return Collection|MEMBER[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(MEMBER $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->addContributedProject($this);
        }

        return $this;
    }

    public function removeMember(MEMBER $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            $member->removeContributedProject($this);
        }

        return $this;
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
            $issue->setProject($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): self
    {
        if ($this->issues->contains($issue)) {
            $this->issues->removeElement($issue);
            // set the owning side to null (unless already changed)
            if ($issue->getProject() === $this) {
                $issue->setProject(null);
            }
        }

        return $this;
    }


}
