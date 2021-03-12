<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Shipping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * gets orders by searching part of user first or last name. If none given, it gets all orders.
     *
     * @param string|null $user a user firstName or lastName
     *
     * @return mixed
     */
    public function findOrdersByName(string $user = null)
    {
        $qb = $this->createQueryBuilder('o');
        if (null !== $user) {
            $orCondition = $qb->expr()
                ->orX(
                    $qb->expr()->like('s.firstName', $qb->expr()->literal('%' . $user . '%')),
                    $qb->expr()->like('s.lastName', $qb->expr()->literal('%' . $user . '%'))
                );
            $qb->join(Shipping::class, 's', 'with', 'o.shipping = s.id')
                ->where($orCondition);
        }

        return $qb->getQuery()
            ->getResult();
    }
}
