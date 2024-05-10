<?php

namespace App\Controller;

use Exception;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\TaskRepository;
use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/project', name: 'project.')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProjectController extends AbstractController
{

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user    = $this->getUser();
        $project = new Project();
        $form    = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $project->setUser($user);
                $project->setCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($project);
                $entityManager->flush();
                
                $this->addFlash('success', 'Projet créer');
                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            }catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            } 
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Project $project, ColumnRepository $colRepo, TaskRepository $taskRepo): Response
    {
        $columns = $colRepo->getColumnsByProject($project);
        // $tasks = $taskRepo->findAll();
        if (!$columns) {
            $columns = [];
        }
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'columns' => $columns
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $entityManager->flush();
                
                $this->addFlash('success', 'Projet éditer');
                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            }catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->getPayload()->get('_token'))) {
            try {

                $entityManager->remove($project);
                $this->addFlash('danger', 'Projet supprimer');
                $entityManager->flush();
            } catch(Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}