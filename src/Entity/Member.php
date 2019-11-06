<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 */
class Member implements UserInterface
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
    private $name;
    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $emailAddress;
    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PROJECT", mappedBy="owner")
     */
    private $ownedProjects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\PROJECT", inversedBy="members")
     */
    private $contributedProjects;

    public function __construct($name, $emailAddress, $password)
    {
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->password = $password;
        $this->ownedProjects = new ArrayCollection();
        $this->contributedProjects = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getRoles()
    {
        return [
            'ROLE_MEMBER'
        ];
    }
    public function getSalt()
    {
        return null;
    }
    public function getUsername()
    {
        return $this->emailAddress;
    }
    public function eraseCredentials() {}
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }
    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
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