<?php
// src/Command/GenerateCategoryCodes.php
namespace App\Command;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCategoryCodes extends Command
{
    protected static $defaultName = 'app:generate-category-codes';
    
    private $categoryRepository;
    private $entityManager;
    
    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $categories = $this->categoryRepository->findAll();
        $count = 0;
        
        foreach ($categories as $category) {
            if (empty($category->getCode())) {
                $category->generateCode();
                $count++;
            }
        }
        
        $this->entityManager->flush();
        
        $io->success(sprintf('Codes générés pour %d catégories', $count));
        
        return Command::SUCCESS;
    }
}