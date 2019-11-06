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
     * @ORM\ManyToMany(targetEntity="App\Entity\PROJECT", inversedBy="members")
     */
    private $projects;
    public function __construct($name, $emailAddress, $password)
    {
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->password = $password;
        $this->projects = new ArrayCollection();
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