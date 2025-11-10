<?php

namespace App\Repository;

use App\Entity\Car;
use App\Enum\CarStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /**
     * Find cars available for rental on a given date.
     * - active
     * - not under maintenance
     * - status Available
     * - insurance not expired on date
     */
    public function findAvailableForDate(?\DateTimeImmutable $on = null): array
    {
        $on ??= new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.active = :true')
            ->andWhere('c.isUnderMaintenance = :false')
            ->andWhere('c.status = :status')
            ->andWhere('c.insuranceExpirationDate IS NULL OR c.insuranceExpirationDate > :on')
            ->setParameter('true', true)
            ->setParameter('false', false)
            ->setParameter('status', CarStatus::Available)
            ->setParameter('on', $on)
            ->orderBy('c.brand', 'ASC')
            ->addOrderBy('c.model', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

