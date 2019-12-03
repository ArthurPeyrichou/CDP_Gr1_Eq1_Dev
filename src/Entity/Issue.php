<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue
{
    public const TODO = 'todo';
    public const DONE = 'done';
    public const DOING = 'doing';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sprint", inversedBy="issues")
     */
    private $sprint;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Task", mappedBy="relatedIssues")
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="issues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    public function __construct($number, $description, $difficulty, $priority, $project, $sprint = null)
    {
        $this->number = $number;
        $this->description = $description;
        $this->difficulty = $difficulty;
        $this->priority = $priority;
        $this->project = $project;
        $this->sprint = $sprint;
        $this->tasks = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): string
    {
        if (count($this->tasks) == 0) {
            return self::TODO;
        }
        
        $cptTodo = 0;
        $cptDone = 0;
        foreach ($this->tasks as $task) {
            switch($task->getStatus()){
                case Task::TODO :
                    $cptTodo++;
                    break;
                case Task::DOING :
                    return self::DOING;
                    break;
                case Task::DONE :
                    $cptDone++;
                    break;
            }
        }  

        if( $cptDone > 0 && $cptTodo == 0 ) {
            return self::DONE;
        } else if($cptDone > 0 && $cptTodo > 0) {
            return self::DOING;
        }
        
        return self::TODO;
        
    }

    public function getProportionOfDone(): string
    {
        if (count($this->tasks) == 0) {
            return "0%";
        }
        
        $cptDone = 0;
        foreach ($this->tasks as $task) {
            if ($task->getStatus() == Task::DONE) {
                $cptDone+=1;
            }
        }  
        
        return intval($cptDone / count($this->tasks) * 100.0) . "%";
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->addRelatedIssue($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->relatedIssues->removeElement($task);
            $task->removeRelatedIssue($this);
        }

        return $this;
    }

}
