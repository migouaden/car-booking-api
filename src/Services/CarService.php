<?php

namespace App\Services;

use App\Entity\Car;
use App\Repository\ReservationRepository;

class CarService{


    private ReservationRepository $reservationRepository;

    public function __construct( ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function isAvailable(Car $car, \DateTimeInterface $start, \DateTimeInterface $end , ?int $excludeId = null): bool
    {
        return !$this->reservationRepository->isCarReservedInPeriod($car, $start, $end , $excludeId);
    }    

}