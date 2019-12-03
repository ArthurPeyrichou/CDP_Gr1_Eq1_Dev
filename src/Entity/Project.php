<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
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
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="ownedProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Member", mappedBy="contributedProjects")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Issue", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $issues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sprint", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $sprints;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Release", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $releases;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Test", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $tests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Documentation", mappedBy="project")
     */
    private $ressourcesDoc;

    public function __construct($owner, $name, $description, $creationDate)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->description = $description;
        $this->creationDate = $creationDate;
        $this->members = new ArrayCollection();
        $this->issues = new ArrayCollection();

        $this->sprints = new ArrayCollection();
        $this->releases = new ArrayCollection();
        $this->ressourcesDoc = new ArrayCollection();
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
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?Member
    {
        return $this->owner;
    }

    public function setOwner(Member $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->addContributedProject($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            $member->removeContributedProject($this);
        }

        return $this;
    }

    /**
     * @return Member[]
     */
    public function getMembersAndOwner(): array
    {
        return array_merge($this->getMembers()->getValues(), [$this->getOwner()]);
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

    /**
     * @return Collection|Sprint[]
     */
    public function getSprints(): Collection
    {
        return $this->sprints;
    }

    public function addSprint(Sprint $sprint): self
    {
        if (!$this->sprints->contains($sprint)) {
            $this->sprints[] = $sprint;
            $sprint->setProject($this);
        }

        return $this;
    }

    public function removeSprint(Sprint $sprint): self
    {
        if ($this->sprints->contains($sprint)) {
            $this->sprints->removeElement($sprint);
            // set the owning side to null (unless already changed)
            if ($sprint->getProject() === $this) {
                $sprint->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Release[]
     */
    public function getReleases(): Collection
    {
        return $this->releases;
    }

    public function addRelease(Release $release): self
    {
        if (!$this->releases->contains($release)) {
            $this->releases[] = $release;
            $release->setProject($this);
        }

        return $this;
    }

    public function removeRelease(Release $release): self
    {
        if ($this->releases->contains($release)) {
            $this->releases->removeElement($release);
            // set the owning side to null (unless already changed)
            if ($release->getProject() === $this) {
                $release->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Test[]
     */
    public function getTests(): Collection
    {
        return $this->tests;
    }

    public function addTest(Test $test): self
    {
        if (!$this->tests->contains($test)) {
            $this->tests[] = $test;
            $test->setProject($this);
        }

        return $this;
    }

    public function removeTest(Test $test): self
    {
        if ($this->tests->contains($test)) {
            $this->tests->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getProject() === $this) {
                $test->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Documentation[]
     */
    public function getRessourcesDoc(): Collection
    {
        return $this->ressourcesDoc;
    }

    public function addRessourcesDoc(Documentation $ressourcesDoc): self
    {
        if (!$this->ressourcesDoc->contains($ressourcesDoc)) {
            $this->ressourcesDoc[] = $ressourcesDoc;
            $ressourcesDoc->setProject($this);
        }

        return $this;
    }

    public function removeRessourcesDoc(Documentation $ressourcesDoc): self
    {
        if ($this->ressourcesDoc->contains($ressourcesDoc)) {
            $this->ressourcesDoc->removeElement($ressourcesDoc);
            // set the owning side to null (unless already changed)
            if ($ressourcesDoc->getProject() === $this) {
                $ressourcesDoc->setProject(null);
            }
        }

        return $this;
    }


}
