<?php
// src/Repository/DocumentRepository.php
namespace App\Repository;

use App\Entity\Category; 
use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function countByCategory(Category $category): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findRecentDocuments(int $limit = 10): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les documents avec un filtre spécifique
     *
     * @param string $filter Type de filtre à appliquer
     * @return Document[]
     */
    public function findAllDocumentsWithFilter(string $filter = 'date-desc'): array
    {
        $qb = $this->createQueryBuilder('d');

        switch ($filter) {
            case 'date-asc':
                $qb->orderBy('d.createdAt', 'ASC');
                break;
            case 'name-asc':
                $qb->orderBy('d.title', 'ASC');
                break;
            case 'type':
                $qb->orderBy('d.format', 'ASC')
                   ->addOrderBy('d.title', 'ASC');
                break;
            case 'date-desc':
            default:
                $qb->orderBy('d.createdAt', 'DESC');
                break;
        }

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Recherche les documents correspondant à un terme de recherche
     *
     * @param string $searchTerm Terme de recherche
     * @return Document[]
     */
    public function searchDocuments(string $searchTerm): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.author', 'a')
            ->where('d.title LIKE :term')
            ->orWhere('d.filename LIKE :term')
            ->orWhere('c.name LIKE :term')
            ->orWhere('a.fullName LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}