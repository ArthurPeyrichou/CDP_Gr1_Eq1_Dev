<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @ORM\Table(name="member",uniqueConstraints={@UniqueConstraint(columns={"PSEUDO", "MAIL"})})
 */

class Member
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50,  unique=true )
     */
    private $PSEUDO;

    /**
     * @ORM\Column(type="string", length=50,  unique=true)
     */
    private $MAIL;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $PASSWORD;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PROJECT", mappedBy="owner")
     */
    private $ownedProjects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PROJECT", inversedBy="members")
     */
    private $contributedProjects;



    public function __construct($PSEUDO, $MAIL, $PASSWORD)
    {
        $this->PSEUDO = $PSEUDO;
        $this->MAIL = $MAIL;
        $this->PASSWORD = $PASSWORD;
        $this->ownedProjects = new ArrayCollection();
        $this->contributedProjects = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getPSEUDO(): ?string
    {
        return $this->PSEUDO;
    }

    public function setPSEUDO(string $PSEUDO): self
    {
        $this->PSEUDO = $PSEUDO;

        return $this;
    }

    public function getMAIL(): ?string
    {
        return $this->MAIL;
    }

    public function setMAIL(string $MAIL): self
    {
        $this->MAIL = $MAIL;

        return $this;
    }

    public function getPASSWORD(): ?string
    {
        return $this->PASSWORD;
    }

    public function setPASSWORD(string $PASSWORD): self
    {
        $this->PASSWORD = $PASSWORD;

        return $this;
    }


    /**
     * @return Collection|PROJECT[]
     */
    public function getOwnedProjects(): Collection
    {
        return $this->ownedProjects;
    }

    public function addOwnedProject(PROJECT $ownedProject): self
    {
        if (!$this->ownedProjects->contains($ownedProject)) {
            $this->ownedProjects[] = $ownedProject;
            $ownedProject->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedProject(PROJECT $ownedProject): self
    {
        if ($this->ownedProjects->contains($ownedProject)) {
            $this->ownedProjects->removeElement($ownedProject);
            // set the owning side to null (unless already changed)
            if ($ownedProject->getOwner() === $this) {
                $ownedProject->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PROJECT[]
     */
    public function getContributedProjects(): Collection
    {
        return $this->contributedProjects;
    }

    public function addContributedProject(PROJECT $contributedProject): self
    {
        if (!$this->contributedProjects->contains($contributedProject)) {
            $this->contributedProjects[] = $contributedProject;
        }

        return $this;
    }

    public function removeContributedProject(PROJECT $contributedProject): self
    {
        if ($this->contributedProjects->contains($contributedProject)) {
            $this->contributedProjects->removeElement($contributedProject);
        }

        return $this;
    }


}
