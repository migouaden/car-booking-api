<?php

namespace App\Repository;

use App\Entity\Car;
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

    public function getAllCars()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getArrayResult();
    }

    public function getCarById(int $id): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.brand, c.model')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
