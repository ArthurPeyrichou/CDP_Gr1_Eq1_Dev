<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity(repositoryClass="App\Repository\INVITATIONRepository")
 */
class INVITATION
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $MEMBER_ID;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $PROJECT_ID;

    /**
     * @ORM\Column(type="datetime" ,options={"default": "CURRENT_TIMESTAMP"})
     */
    private $DATE;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $INV_KEY;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PROJECT", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MEMBER", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    public function __construct($MEMBER_ID, $PROJECT_ID)
    {
        $this->$MEMBER_ID= $MEMBER_ID;
        $this->$PROJECT_ID= $PROJECT_ID;
    }
    public function getMEMBERID(): ?int
    {
        return $this->MEMBER_ID;
    }

    public function setMEMBERID(int $MEMBER_ID): self
    {
        $this->MEMBER_ID = $MEMBER_ID;

        return $this;
    }

    public function getPROJECTID(): ?int
    {
        return $this->PROJECT_ID;
    }

    public function setPROJECTID(int $PROJECT_ID): self
    {
        $this->PROJECT_ID = $PROJECT_ID;

        return $this;
    }

    public function getDATE(): ?\DateTimeInterface
    {
        return $this->DATE;
    }

    public function setDATE(\DateTimeInterface $DATE): self
    {
        $this->DATE = $DATE;

        return $this;
    }

    public function getINVKEY(): ?string
    {
        return $this->INV_KEY;
    }

    public function setINVKEY(string $INV_KEY): self
    {
        $this->INV_KEY = $INV_KEY;

        return $this;
    }

    public function getProject(): ?PROJECT
    {
        return $this->project;
    }

    public function setProject(PROJECT $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getMember(): ?MEMBER
    {
        return $this->member;
    }

    public function setMember(MEMBER $member): self
    {
        $this->member = $member;

        return $this;
    }
}
