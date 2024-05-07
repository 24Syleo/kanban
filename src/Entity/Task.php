<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, minMessage: 'Too short min 5 characteres')]
    #[Assert\Length(max: 100, maxMessage: 'Too long max 100 characteres')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'Too long max 1000 characteres')]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $deadLine = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Column $position = null;

    /**
     * @var Collection<int, TaskFiles>
     */
    #[ORM\OneToMany(targetEntity: TaskFiles::class, mappedBy: 'task')]
    private Collection $taskFiles;

    public function __construct()
    {
        $this->taskFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeImmutable
    {
        return $this->deadLine;
    }

    public function setDeadLine(\DateTimeImmutable $deadLine): static
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    public function getPosition(): ?Column
    {
        return $this->position;
    }

    public function setPosition(?Column $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, TaskFiles>
     */
    public function getTaskFiles(): Collection
    {
        return $this->taskFiles;
    }

    public function addTaskFile(TaskFiles $taskFile): static
    {
        if (!$this->taskFiles->contains($taskFile)) {
            $this->taskFiles->add($taskFile);
            $taskFile->setTask($this);
        }

        return $this;
    }

    public function removeTaskFile(TaskFiles $taskFile): static
    {
        if ($this->taskFiles->removeElement($taskFile)) {
            // set the owning side to null (unless already changed)
            if ($taskFile->getTask() === $this) {
                $taskFile->setTask(null);
            }
        }

        return $this;
    }
}