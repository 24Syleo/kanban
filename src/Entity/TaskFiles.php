<?php

namespace App\Entity;

use App\Repository\TaskFilesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskFilesRepository::class)]
class TaskFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filesName = null;

    #[ORM\ManyToOne(inversedBy: 'taskFiles')]
    private ?Task $task = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilesName(): ?string
    {
        return $this->filesName;
    }

    public function setFilesName(string $filesName): static
    {
        $this->filesName = $filesName;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }
}
