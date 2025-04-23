<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UxPackageRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class DashboardController extends AbstractController
{
    #[Route("/dashboard", name: "app_dashboard")]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("app_login");
        }
        return $this->render("dashboard/index.html.twig", [
            "controller_name" => "DashboardController",
        ]);
    }

    #[Route("/dash", name: "app")]
    public function indetestx(): Response
    {
        return $this->render("dashboard/testdrop.html.twig", [
            "controller_name" => "DashboardController",
        ]);
    }
}
