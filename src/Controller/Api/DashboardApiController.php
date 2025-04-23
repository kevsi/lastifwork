<?php
// src/Controller/Api/DashboardApiController.php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[Route("/api/dashboard")]
class DashboardApiController extends AbstractController
{
    #[Route("/", name: "api_dashboard", methods: ["GET"])]
    public function index(Request $request, JsonResponse $data): Response
    {
        if ($request->headers->get("Accept") === "application/json") {
            return $this->json($data);
        }

        return $this->render("dashboard/index.html.twig", [
            "controller_name" => "DashboardApiController",
        ]);
    }
}
