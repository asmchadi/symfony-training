<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Gets all products related on the categories which  contain the product.
     *
     * @param Product $product the product
     *
     * @return array<mixed> the related products depending on the categories
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getRelatedProducts(Product $product): array
    {
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('SELECT * FROM   product
                           WHERE  id IN 
                           (
                                SELECT product_id FROM   category_product
                                WHERE  category_id 
                                IN (
                                    SELECT category_id FROM   category_product WHERE  product_id = :p
                                )) '
            );
        $stmt->execute([
            'p' => $product->getId(),
        ]);

        return $stmt->fetchAllAssociative();
    }

    /**
     * Gets all products with quantity criteria.
     *
     * @param int $min the minimum quantity. -1 if you wish to disable the criteria
     * @param int $max the maximum quantity. -1 if you wish to disable the criteria
     *
     * @return array<mixed>
     */
    public function getProductsWithQuantity(int $min = -1, int $max = -1)
    {
        $qb = $this->createQueryBuilder('p');
        if ($min > 0) {
            $qb->where('p.quantity > :min')
                ->setParameter('min', $min);
        }

        if ($max > 0) {
            $qb->andWhere('p.quantity < :max')
                ->setParameter('max', $max);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Searches products by given criteria.
     *
     * @param array $criteria set of criteria. Example term
     *
     * @return array<mixed> the resulting data.
     */
    public function search(array $criteria)
    {
        $qb = $this->createQueryBuilder('p');
        $expr = $qb->expr();

        return $qb->where($expr->like('p.label', $expr->literal('%'.$criteria['term'].'%')))
            ->getQuery()
            ->getResult();
    }
}
