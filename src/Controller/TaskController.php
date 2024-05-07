<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ColumnRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            $task->setPosition($column);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task created');
            return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'project' => $project,
        ]);
    }

    #[Route('/{task_id}', name: 'show', methods: ['GET'])]
    public function show(TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $column = $colRepo->find($column_id);
        $task = $taskRepo->find($task_id);
        return $this->render('task/show.html.twig', [
            'task' => $task,
            'project' => $project,
            'column' => $column,
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
            $entityManager->flush();
            $this->addFlash('success', 'Task edited successfully');
            return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'project' => $project,
            'column' => $column,
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{task_id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager, TaskRepository $taskRepo, int $task_id, Project $project, ColumnRepository $colRepo, int $column_id): Response
    {
        $column = $colRepo->find($column_id);
        $task = $taskRepo->find($task_id);
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }
        $this->addFlash('danger', 'Task deleted successfully');
        return $this->redirectToRoute('project.show', ["id" => $project->getId(), "column_id" => $column->getId()], Response::HTTP_SEE_OTHER);
    }
}
