<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MEMBERRepository")
 * @ORM\Table(name="member",uniqueConstraints={@UniqueConstraint(columns={"PSEUDO", "MAIL"})})
 */

class MEMBER
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
     * @ORM\ManyToMany(targetEntity="App\Entity\PROJECT", inversedBy="members")
     */
    private $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(PROJECT $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(PROJECT $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
        }

        return $this;
    }
}
