<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PROJECTRepository")
 */
class PROJECT
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */

    private $NAME;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $DESCRIPTION;

    /**
     * @ORM\Column(type="integer")
     */
    private $MANAGER_ID;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MEMBER", mappedBy="projects")
     */
    private $members;

    public function __construct($MANAGER_ID, $NAME, $DESCRIPTION)
    {
        $this->MANAGER_ID = $MANAGER_ID;
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

    public function getMANAGERID(): ?int
    {
        return $this->MANAGER_ID;
    }

    public function setMANAGERID(int $MANAGER_ID): self
    {
        $this->MANAGER_ID = $MANAGER_ID;

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
            $member->addProject($this);
        }

        return $this;
    }

    public function removeMember(MEMBER $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            $member->removeProject($this);
        }

        return $this;
    }
}
