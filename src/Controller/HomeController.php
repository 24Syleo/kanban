<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        if ($user) {
            $projects = $projectRepository->findByUser($user);
        } else {
            $projects = [];
        }


        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
