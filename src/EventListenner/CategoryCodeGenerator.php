<?php
// src/EventListener/CategoryCodeGenerator.php

namespace App\EventListener;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CategoryCodeGenerator
{
    private $categoryRepository;
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    
    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (!$entity instanceof Category) {
            return;
        }
        
        $this->handleCodeGeneration($entity);
    }
    
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (!$entity instanceof Category) {
            return;
        }
        
        $this->handleCodeGeneration($entity);
    }
    
    private function handleCodeGeneration(Category $category)
    {
        // Génère le code de base
        $category->generateCode();
        
        // Vérifie s'il y a une collision
        $baseCode = $category->getCode();
        $counter = 1;
        
        // Tant qu'un code identique existe déjà pour une autre catégorie
        while ($this->codeExists($category->getId(), $category->getCode())) {
            // Ajoute un suffixe numérique
            $category->setCode($baseCode . $counter);
            $counter++;
        }
    }
    
    private function codeExists($currentId, $code)
    {
        $existingCategory = $this->categoryRepository->findOneBy(['code' => $code]);
        
        // Le code existe déjà pour une autre catégorie
        return $existingCategory && $existingCategory->getId() !== $currentId;
    }
}