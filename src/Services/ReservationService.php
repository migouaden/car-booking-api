<?php

namespace App\Services;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService{

    private EntityManagerInterface $entityManager;

    private CarService $car_service;

    public function __construct(EntityManagerInterface $entityManager , CarService $car_service)
    {
        $this->entityManager = $entityManager;
        $this->car_service = $car_service;
    }

    //créer une réservation
    public function create(User $user, Car $car, \DateTimeInterface $start, \DateTimeInterface $end): Reservation
    {
        //vérification des dates de reservation
        if ($start > $end) {
            throw new \InvalidArgumentException('La date de fin ne doit pas être antérieure à la date de début.');
        }

        //vérification disponibilité de véhicule
        if (!$this->car_service->isAvailable($car, $start, $end)) {
            throw new \DomainException('Voiture non disponible pour cette période');
        }

        //inserer la reservation
        $reservation = new Reservation();

        $reservation->setUser($user);
        $reservation->setCar($car);
        $reservation->setStartDate($start);
        $reservation->setEndDate($end);
        $reservation->setDateCreated(new \DateTime());

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }

    //update réserevation
    public function update(Reservation $reservation, \DateTimeInterface $start, \DateTimeInterface $end): Reservation
    {
        //verification les dates de réservation
        if ($start > $end) {
            throw new \InvalidArgumentException('La date de fin ne doit pas être antérieure à la date de début.');
        }

        // Vérifier disponibilité de véhicule (en excluant cette réservation)
        $conflict = !$this->car_service->isAvailable(
            $reservation->getCar(),
            $start,
            $end,
            $reservation->getId()
            );

        if ($conflict) {
            throw new \DomainException('Voiture non disponible');
        }

        $reservation->setStartDate($start);
        $reservation->setEndDate($end);

        $this->entityManager->flush();

        return $reservation;
    }

    //Supprimer la réservation
    public function delete(Reservation $reservation): void
    {

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();
    }

    //Réservation response result
    public function formatReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->getId(),
            'startDate' => $reservation->getStartDate()->format('Y-m-d'),
            'endDate' => $reservation->getEndDate()->format('Y-m-d'),
            'car' => [
                'id' => $reservation->getCar()->getId(),
                'brand' => $reservation->getCar()->getBrand(),
                'model' => $reservation->getCar()->getModel(),
            ]
        ];
    }
    
}