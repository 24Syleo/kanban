<?php

namespace App\Controller;

use App\Entity\Column;
use App\Entity\Project;
use App\Form\ColumnType;
use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project/{id}/column', name: 'column.')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ColumnController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Project $project): Response
    {
        $column = new Column();
        $form = $this->createForm(ColumnType::class, $column);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $column->setProject($project);
            $entityManager->persist($column);
            $entityManager->flush();

            return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('column/new.html.twig', [
            'column' => $column,
            'form' => $form,
            'project' => $project
        ]);
    }

    #[Route('/{column_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ColumnRepository $repo, EntityManagerInterface $entityManager, int $column_id, Project $project): Response
    {
        $column = $repo->find($column_id);
        $form = $this->createForm(ColumnType::class, $column);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('column/edit.html.twig', [
            'column' => $column,
            'form' => $form,
            'project' => $project
        ]);
    }

    #[Route('/{column_id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, ColumnRepository $repo, EntityManagerInterface $entityManager, Project $project, int $column_id): Response
    {
        $column = $repo->find($column_id);
        if ($this->isCsrfTokenValid('delete' . $column->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($column);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project.show', ["id" => $project->getId()], Response::HTTP_SEE_OTHER);
    }
}
