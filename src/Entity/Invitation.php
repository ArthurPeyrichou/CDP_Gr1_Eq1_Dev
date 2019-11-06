<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use \Datetime;

/**
 * @ORM\Entity(repositoryClass="InvitationRepository")
 */
class Invitation
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
     * @ORM\OneToOne(targetEntity="App\Entity\Member", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    public function __construct($MEMBER_ID, $PROJECT_ID)
    {
        $this->MEMBER_ID= $MEMBER_ID;
        $this->PROJECT_ID= $PROJECT_ID;
        $this->INV_KEY = $this->addRandomPassword();
        //Les dates seront affichées au format Français (jj/mm/aaaa) mais seront stockées et traitées par l'application au format US (yyyy-mm-dd).
        $dt = new DateTime('now');
        $this->DATE = $dt;
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

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    private function addRandomPassword(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 15; ++$i) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    } 
}
