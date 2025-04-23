<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use  Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Entity\Category;
use App\Form\DocumentType;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\UserRepository;

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
        CategoryRepository $categoryRepository
    ): Response {
        $document = new Document();
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
            return $this->redirectToRoute('app_docs');
        }

        return $this->render('documents/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }
    #[Route('/documents', name: 'app_docs')]
    public function index(): Response
    {
        return $this->render('documents/docs.html.twig', [
            'controller_name' => 'DocumentsController',
        ]);
    }

   

    #[Route('/documents/numerise', name: 'app_docs_numerise')]
    public function numerise(): Response
    {
        return $this->render('documents/numerise.html.twig', [
            'controller_name' => 'DocumentsController',
        ]);
    }
}
