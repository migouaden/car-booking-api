<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findReservationsByUser(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.id, r.startDate, r.endDate, c.id AS car_id, c.brand, c.model')
            ->join('r.car', 'c')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }


    public function isCarReservedInPeriod(Car $car, \DateTimeInterface $start, \DateTimeInterface $end , ?int $excludeId = null): bool
    {
        $query = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.car = :car')
            ->andWhere('r.startDate <= :end')
            ->andWhere('r.endDate >= :start')
            ->setParameter('car', $car)
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ($excludeId !== null) {
            $query->andWhere('r.id != :excludeId')
            ->setParameter('excludeId', $excludeId);
        }

        return (int) $query->getQuery()->getSingleScalarResult() > 0;
    }


}
