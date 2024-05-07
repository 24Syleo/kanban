<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Project;
use App\Entity\TaskFiles;
use App\Form\TaskFilesType;
use App\Repository\TaskRepository;
use App\Repository\ColumnRepository;
use App\Repository\TaskFilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/project/{id}/column/{column_id}/task/{task_id}/files', name: 'files.')]
class TaskFilesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(TaskFilesRepository $taskFilesRepository): Response
    {
        return $this->render('task_files/index.html.twig', [
            'task_files' => $taskFilesRepository->findAll(),
        ]);
    }

    #[Route('/{taskFile_id}', name: 'show', methods: ['GET'])]
    public function show(TaskFilesRepository $fileRepo, int $taskFile_id, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id,): Response
    {
        $column = $colRepo->find($column_id);
        $task = $taskRepo->find($task_id);
        $taskFile = $fileRepo->find($taskFile_id);
        return $this->render('task_files/show.html.twig', [
            'task_file' => $taskFile,
        ]);
    }

    #[Route('/{taskFile_id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, TaskFilesRepository $fileRepo, int $taskFile_id, TaskFiles $taskFile, EntityManagerInterface $entityManager, Project $project): Response
    {
        $taskFile = $fileRepo->find($taskFile_id);
        if ($this->isCsrfTokenValid('delete' . $taskFile->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($taskFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project.show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
    }
}