<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Category;
use App\Form\DocumentType;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Twig\Extension\IconColorExtension;


final class DocumentsController extends AbstractController
{
    private $security;

    
    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        CategoryRepository $categoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
    }
    
    #[Route('/documents/create', name: 'app_docs_create')]
    public function create(
        Request $request, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger, 
        CategoryRepository $categoryRepository): Response {
    
        $document = new Document();
    
        // Récupérer category_id s'il existe dans la requête
        $categoryId = $request->query->get('category_id');
        if ($categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $document->setCategory($category);
            }
        }
    
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        $categories = $categoryRepository->findAll();
        
        if ($form->isSubmitted() && $form->isValid()) {
        // Get the current user
            $user = $this->security->getUser();
            $document->setUser($user);
            
            // Handle file upload
            $documentFile = $form->get('documentFile')->getData();
            if ($documentFile instanceof UploadedFile) {
                $originalFilename = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $documentFile->guessExtension();
                try {
                    $documentFile->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                    $document->setFilePath($newFilename);
                    $document->setMimeType($documentFile->getMimeType());
                    $document->setSize($documentFile->getSize());
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier');
                    return $this->redirectToRoute('app_docs_create');
                }
            }
        
        $entityManager->persist($document);
        $entityManager->flush();
        
        $this->addFlash('success', 'Document créé avec succès!');
        
        // Rediriger vers la catégorie si elle existe
        if ($categoryId) {
            return $this->redirectToRoute("app_category_show", ['id' => $categoryId]);
        }
        
        return $this->redirectToRoute('app_docs');
    }
    
    return $this->render('documents/create.html.twig', [
        'form' => $form->createView(),
        'categories' => $categories,
        'category_id' => $categoryId,
    ]);
}
    
    #[Route('/documents', name: 'app_docs')]
    public function index(DocumentRepository $documentRepository): Response
    {
        $recentDocuments = $documentRepository->findRecentDocuments(10);
        $totalDocuments = $documentRepository->count([]);
        $hasMoreDocuments = $totalDocuments > 10;
        $getIconColor = function($type) {
            return match(strtolower($type)) {
                'rapport' => 'info',
                'contrat' => 'success',
                'facture' => 'warning',
                'procedure' => 'primary',
                'note' => 'danger',
                default => 'secondary'
            };
        };
        $getFormatColor = function($format) {
            return match(strtolower($format)) {
                'pdf' => 'success',
                'docx', 'doc' => 'info',
                'xlsx', 'xls' => 'warning',
                'txt' => 'danger',
                'png', 'jpg', 'jpeg' => 'primary',
                default => 'secondary'
            };
        };
        return $this->render('documents/docs.html.twig', [
            'recentDocuments' => $recentDocuments,
            'hasMoreDocuments' => $hasMoreDocuments,
            'getIconColor' => $getIconColor,
            'getFormatColor' => $getFormatColor,
        ]);
    }
    #[Route('/documents/show', name: 'app_docs_show')]
    public function show(Document $document): Response
    {
        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/documents/all', name: 'app_docs_all')]
    public function allDocuments(Request $request, DocumentRepository $documentRepository): Response
    {
        // Récupérer les paramètres de filtrage
        $filter = $request->query->get('filter', 'date-desc');
        
        // Récupérer les documents selon le filtre
        $documents = $documentRepository->findAllDocumentsWithFilter($filter);
        
        // Créer une fonction d'assistance pour déterminer la couleur de l'icône
        $getIconColor = function($type) {
            return match(strtolower($type)) {
                'rapport' => 'info',
                'contrat' => 'success',
                'facture' => 'warning',
                'procedure' => 'primary',
                'note' => 'danger',
                default => 'secondary'
            };
        };
        
        // Créer une fonction d'assistance pour déterminer la couleur du format
        $getFormatColor = function($format) {
            return match(strtolower($format)) {
                'pdf' => 'success',
                'docx', 'doc' => 'info',
                'xlsx', 'xls' => 'warning',
                'txt' => 'danger',
                'png', 'jpg', 'jpeg' => 'primary',
                default => 'secondary'
            };
        };
        
        return $this->render('documents/all.html.twig', [
            'documents' => $documents,
            'currentFilter' => $filter,
            'getIconColor' => $getIconColor,
            'getFormatColor' => $getFormatColor,
        ]);
    }

    #[Route('/documents/{id}', name: 'app_docs_view', defaults: ['requirements' => ['id' => '\d+']])]
    public function view(Document $document): Response
    {
        return $this->render('document/view.html.twig', [
            'document' => $document,
        ]);
    }
    
    
    #[Route('/documents/{id}/download', name: 'app_docs_download', defaults: ['requirements' => ["id" => "\d+"] ])]
    public function download(Document $document): Response
    {
        // Implémentation du téléchargement
        // ...
        
        return $this->file($document->getFilePath(), $document->getFilename());
    }
   

    #[Route('/documents/numerise', name: 'app_docs_numerise')]
    public function numerise(): Response
    {
        return $this->render('documents/numerise.html.twig', [
            'controller_name' => 'DocumentsController',
        ]);
    }
}
