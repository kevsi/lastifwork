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

#[Route('/categories')]
class CategoriesController extends AbstractController
{
    /**
     * Liste des catégories (vue administrateur)
     */
    #[Route('', name: 'app_categories', methods: ['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        DocumentRepository $documentRepository
    ): Response {
        // Récupérer les catégories racines (sans parent)
        $rootCategories = $categoryRepository->findBy(['parent' => null]);

    // Charge récursivement les enfants
        $categoryRepository->loadChildCategories($rootCategories);
        
        // Calculer le nombre de documents par catégorie pour toutes les catégories
        $categories = $categoryRepository->findAll();
        $filesCounts = [];
        foreach ($categories as $category) {
            $filesCounts[$category->getId()] = $documentRepository->countByCategory($category);
        }
        
        return $this->render('category/index.html.twig', [
            'root_categories' => $rootCategories,
            'subcategories' => $rootCategories,
            'documents' => [],
            'categories' => $categories,
            'filesCounts' => $filesCounts,
            'statusOptions' => CategoryStatus::cases(),
        ]);
    }
    
    /**
     * Afficher une catégorie spécifique (explorateur)
     */
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(
        int $id, 
        CategoryRepository $categoryRepository, 
        DocumentRepository $documentRepository
    ): Response {
        // Récupérer la catégorie actuelle
        $category = $categoryRepository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        // Récupérer les catégories racines (pour le menu latéral)
        $rootCategories = $categoryRepository->findBy(['parent' => null]);
        $categoryRepository->loadChildCategories($rootCategories);
        
        // Récupérer les sous-catégories de la catégorie actuelle
        $subcategories = $categoryRepository->findBy(['parent' => $category]);
        
        // Récupérer les documents de la catégorie actuelle
        $documents = $documentRepository->findBy(['category' => $category]);
        
        // Construire le chemin (fil d'Ariane)
        $path = $categoryRepository->buildCategoryPath($category);

        
        return $this->render('category/index.html.twig', [
            'root_categories' => $rootCategories,
            'current_category' => $category,
            'subcategories' => $subcategories,
            'documents' => $documents,
            'current_path' => $path,
            'root_categories' => $rootCategories,
            'statusOptions' => CategoryStatus::cases(), // Ajoutez cette ligne
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    
    /**
     * Créer une nouvelle catégorie
     */
    /**
 * Créer une nouvelle catégorie
 */
    #[Route('/new', name: 'app_categories_create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        CategoryRepository $categoryRepository
    ): Response {
        $category = new Category();
    
    // Récupérer le parent_id s'il existe dans la requête
    $parentId = $request->query->get('parent_id');
    if ($parentId) {
        $parentCategory = $categoryRepository->find($parentId);
        if ($parentCategory) {
            $category->setParent($parentCategory);
        }
    }
    
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Si le slug n'est pas fourni, le générer automatiquement
        if (empty($category->getSlug())) {
            $slug = $slugger->slug($category->getName())->lower();
            $category->setSlug($slug);
        }

        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash("success", "Catégorie créée avec succès");

        // Rediriger vers la catégorie parente si elle existe
        if ($parentId) {
            return $this->redirectToRoute("app_category_show", ['id' => $parentId]);
        }
        
        return $this->redirectToRoute("app_categories");
    }

    return $this->render("category/create.html.twig", [
        "form" => $form->createView(),
        "parent_id" => $parentId,
    ]);
    }

    /**
     * Modifier une catégorie
     */
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
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

        return $this->render("category/edit.html.twig", [
            "category" => $category,
            "form" => $form->createView(),
            "filesCount" => $filesCount,
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    #[Route('/{id}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Category $category,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                "delete-category-" . $category->getId(),
                $request->request->get("_token")
            )
        ) {
            $entityManager->remove($category);
            $entityManager->flush();
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