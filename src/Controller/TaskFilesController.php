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

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taskFile = new TaskFiles();
        $form = $this->createForm(TaskFilesType::class, $taskFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taskFile);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_files_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_files/new.html.twig', [
            'task_file' => $taskFile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(TaskFiles $taskFile): Response
    {
        return $this->render('task_files/show.html.twig', [
            'task_file' => $taskFile,
        ]);
    }

    #[Route('/{taskFile_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskFilesRepository $fileRepo, int $taskFile_id, EntityManagerInterface $entityManager, Project $project, int $task_id): Response
    {
        $taskFile = $fileRepo->find($taskFile_id);
        $form = $this->createForm(TaskFilesType::class, $taskFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Fichier ajouté');
            return $this->redirectToRoute('task.show', ['task_id' => $task_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_files/edit.html.twig', [
            'task_file' => $taskFile,
            'form' => $form,
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