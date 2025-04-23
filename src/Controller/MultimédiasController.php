<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MultimédiasController extends AbstractController
{
    #[Route('/multimedias', name: 'app_multimedias')]
    public function index(): Response
    {
        return $this->render('multimedias/index.html.twig', [
            'controller_name' => 'MultimédiasController',
        ]);
    }
    #[Route('/multimedias/import', name: 'app_multimedias_import')]
    public function import(): Response
    {
        return $this->render('multimedias/import.html.twig', [
            'controller_name' => 'MultimédiasController',
        ]);
    }
}
