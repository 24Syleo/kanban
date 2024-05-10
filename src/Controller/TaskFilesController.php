<?php

namespace App\Controller;

use Exception;
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
    public function show(TaskFilesRepository $fileRepo, int $taskFile_id, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        try{
            $column = $colRepo->find($column_id);
            $task = $taskRepo->find($task_id);
            $taskFile = $fileRepo->find($taskFile_id);
        } catch(Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }
        return $this->render('task_files/show.html.twig', [
            'task_file' => $taskFile,
            'project' => $project,
            'column' => $column,
            'task' => $task
        ]);
    }

    #[Route('/{taskFile_id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, TaskFilesRepository $fileRepo, int $taskFile_id, EntityManagerInterface $entityManager, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $taskFile = $fileRepo->find($taskFile_id);
        if ($this->isCsrfTokenValid('delete' . $taskFile->getId(), $request->getPayload()->get('_token'))) {
            try {
                $entityManager->remove($taskFile);
                $this->addFlash('danger', 'fichier supprimé');
                $entityManager->flush();
            }catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->redirectToRoute('project.show', ["id" => $project->getId(), "column_id" => $column_id, 'task_id' => $task_id], Response::HTTP_SEE_OTHER);
    }
}