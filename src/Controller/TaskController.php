<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Project;
use App\Entity\TaskFiles;
use App\Form\TaskFilesType;
use App\Repository\TaskRepository;
use App\Repository\ColumnRepository;
use App\Repository\TaskFilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/project/{id}/column/{column_id}/task', name: 'task.')]
class TaskController extends AbstractController
{

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $column = $colRepo->find($column_id);
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['projet' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $task->setPosition($column);
                $entityManager->persist($task);
                $entityManager->flush();
                
                $this->addFlash('success', 'Task created');
                return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
            }catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/{task_id}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id, TaskFilesRepository $taskFilesRepository): Response
    {
        $column = $colRepo->find($column_id);
        $task = $taskRepo->find($task_id);
        $task_files = $taskFilesRepository->findAll();

        $taskFile = new TaskFiles();
        $form = $this->createForm(TaskFilesType::class, $taskFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $taskFile->setTask($task);
                $entityManager->persist($taskFile);
                $entityManager->flush();
                $this->addFlash('success', 'fichier ajouté');
                return $this->redirectToRoute('task.show', ["id" => $project->getId(), "column_id" => $column_id, 'task_id' => $task_id], Response::HTTP_SEE_OTHER);
            } catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }
        return $this->render('task/show.html.twig', [
            'task' => $task,
            'project' => $project,
            'column' => $column,
            'form' => $form,
            'task_files' => $task_files
        ]);
    }

    #[Route('/{task_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $column = $colRepo->find($column_id);
        $task = $taskRepo->find($task_id);
        $form = $this->createForm(TaskType::class, $task, ['projet' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $entityManager->flush();
                $this->addFlash('success', 'Task edited successfully');
                return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
            } catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('task/edit.html.twig', [
            'project' => $project,
            'column' => $column,
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{task_id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $task = $taskRepo->find($task_id);
        $column = $colRepo->find($column_id);

        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->get('_token'))) {
            try{

                $entityManager->remove($task);
                $this->addFlash('danger', 'Task deleted successfully');
                $entityManager->flush();
            } catch(Exception $e) {
                $this->addFlash('danger', "il faut supprimer les fichiers");
            }
        }
        return $this->redirectToRoute('project.show', ["id" => $project->getId(), "column_id" => $column->getId()], Response::HTTP_SEE_OTHER);
    }
}