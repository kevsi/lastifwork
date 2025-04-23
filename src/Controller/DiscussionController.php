<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DiscussionController extends AbstractController
{
    #[Route('/discussion', name: 'app_discussion')]
    public function index(): Response
    {
        // Simulation : liste des groupes
        $groupes = [
            ['id' => 1, 'nom' => 'Département Informatique'],
            ['id' => 2, 'nom' => 'Comité Pédagogique'],
            ['id' => 3, 'nom' => 'Club IA & Big Data'],
        ];
        return $this->render('discussion/liste.html.twig', [
            'controller_name' => 'DiscussionController',
        ]);
    }
    #[Route('/discussion/groupe/{id}', name: 'app_discussion_groupe')]
    public function discussionGroupe(int $id): Response
    {
            // Tu peux récupérer le groupe et ses messages ici selon l’ID
            // (Ici encore, on simule juste)
            
            return $this->render('discussion/groupe.html.twig', [
                'groupeId' => $id,
                // Simule ici les données du groupe + messages si besoin
            ]);
    }
    }

