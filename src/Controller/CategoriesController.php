<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Enum\CategoryStatus;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesController extends AbstractController
{
    #[Route("/categories", name: "app_categories")]
    public function index(
        CategoryRepository $categoryRepository,
        DocumentRepository $documentRepository
    ): Response {
        $categories = $categoryRepository->findAll();

        // Calculer le nombre de documents par catégorie
        $filesCounts = [];
        foreach ($categories as $category) {
            $filesCounts[
                $category->getId()
            ] = $documentRepository->countByCategory($category);
        }

        return $this->render("categories/index.html.twig", [
            "categories" => $categories,
            "filesCounts" => $filesCounts,
            "statusOptions" => CategoryStatus::cases(),
        ]);
    }

    #[Route("/categories/new", name: "app_categories_create")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le slug n'est pas fourni, le générer automatiquement
            if (empty($category->getSlug())) {
                $slug = $slugger->slug($category->getName())->lower();
                $category->setSlug($slug);
            }

            // Les dates sont déjà gérées dans le constructeur et les callbacks d'entité

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash("success", "Catégorie créée avec succès");

            return $this->redirectToRoute("app_categories");
        }

        return $this->render("categories/create.html.twig", [
            "form" => $form->createView(),
        ]);
    }

    #[
        Route(
            "/categories/{id}/edit",
            name: "app_category_edit",
            methods: ["GET", "POST"]
        )
    ]
    public function edit(
        Request $request,
        Category $category,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        DocumentRepository $documentRepository
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $filesCount = $documentRepository->countByCategory($category);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le slug n'est pas fourni, le générer automatiquement
            if (empty($category->getSlug())) {
                $slug = $slugger->slug($category->getName())->lower();
                $category->setSlug($slug);
            }

            $entityManager->flush();

            $this->addFlash("success", "Catégorie modifiée avec succès");

            return $this->redirectToRoute("app_categories");
        }

        return $this->render("categories/edit.html.twig", [
            "category" => $category,
            "form" => $form->createView(),
            "filesCount" => $filesCount,
        ]);
    }

    #[
        Route(
            "/categories/{id}/delete",
            name: "app_category_delete",
            methods: ["POST"]
        )
    ]
    public function delete(
        Request $request,
        Category $category,
        EntityManagerInterface $em
    ): Response {
        if (
            $this->isCsrfTokenValid(
                "delete-category-" . $category->getId(),
                $request->request->get("_token")
            )
        ) {
            $em->remove($category);
            $em->flush();
            $this->addFlash("success", "Catégorie supprimée avec succès.");
        } else {
            $this->addFlash(
                "error",
                "Échec de la suppression : jeton CSRF invalide."
            );
        }

        return $this->redirectToRoute("app_categories");
    }
}
