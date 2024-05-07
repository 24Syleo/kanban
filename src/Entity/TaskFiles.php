<?php

namespace App\Entity;

use App\Repository\TaskFilesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: TaskFilesRepository::class)]
#[UniqueEntity('filesName', message: 'Name already exist')]
#[Vich\Uploadable]
class TaskFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'files', fileNameProperty: 'filesName')]
    private ?File $taskFile = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    public function getTaskFile(): ?File
    {
        return $this->taskFile;
    }

    public function setTaskFile(?File $taskFile = null): static
    {
        $this->taskFile = $taskFile;

        return $this;
    }
}