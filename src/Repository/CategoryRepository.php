<?php

namespace App\Repository;

use Doctrine\DBAL\Exception;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function fixEmptyStatuses(string $defaultStatus = 'active'): int
    {
    $conn = $this->getEntityManager()->getConnection();

    $sql = 'UPDATE categories SET status = :defaultStatus WHERE status = ""';

    $stmt = $conn->prepare($sql);
    $stmt->bindValue('defaultStatus', $defaultStatus);

    return $stmt->executeStatement();
    }

     /**
     * Charge récursivement les enfants des catégories
     */
    public function loadChildCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $children = $category->getChildren(); // Doctrine gère ça

            if (!$children->isEmpty()) {
            // Appel récursif
            $this->loadChildCategories($children->toArray());
            }
        }
    }

    
    /**
     * Construit le chemin de la catégorie (pour le fil d'Ariane)
     */
    public function buildCategoryPath(Category $category): array
    {
        $path = [$category];
        $current = $category;
        
        // Remonter l'arborescence des parents
        while ($parent = $current->getParent()) {
            array_unshift($path, $parent);
            $current = $parent;
        }
        
        return $path;
    }


    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
