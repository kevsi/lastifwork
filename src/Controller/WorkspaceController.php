<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkspaceController extends AbstractController
{
    #[Route('/workspace', name: 'app_workspace')]
    public function index(): Response
    {
        return $this->render('workspace/workspace.html.twig', [
            'controller_name' => 'WorkspaceController',
        ]);
    }

    #[Route('/workspace/create', name: 'app_workspace_create')]
    public function create(): Response
    {
        return $this->render('workspace/create.html.twig', [
            'controller_name' => 'WorkspaceController',
        ]);
    }
}
