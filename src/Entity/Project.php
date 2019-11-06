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

    private $NAME;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $DESCRIPTION;

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

    public function __construct($owner, $NAME, $DESCRIPTION)
    {
        $this->$owner = $owner;
        $this->NAME = $NAME;
        $this->DESCRIPTION = $DESCRIPTION;
        $this->members = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNAME(): ?string
    {
        return $this->NAME;
    }

    public function setNAME(string $NAME): self
    {
        $this->NAME = $NAME;

        return $this;
    }

    public function getDESCRIPTION(): ?string
    {
        return $this->DESCRIPTION;
    }

    public function setDESCRIPTION(string $DESCRIPTION): self
    {
        $this->DESCRIPTION = $DESCRIPTION;

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


    public function getCreationDate(): ?\DateTimeInterface
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


}
