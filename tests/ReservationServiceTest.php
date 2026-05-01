<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Car;
use App\Entity\Reservation;
use App\Services\CarService;
use App\Services\ReservationService;

class ReservationServiceTest extends TestCase
{
    private $carService;
    private $reservationRepository;
    private $entityManager;
    private $service;

    protected function setUp(): void
    {
        $this->carService = $this->createMock(CarService::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->service = new ReservationService(
            $this->entityManager,          
            $this->carService            
        );
    }

    public function testCreateReservationSuccess(): void
    {
        $this->carService->method('isAvailable')->willReturn(true);

        $this->entityManager->expects($this->once())->method('persist');

        $this->entityManager->expects($this->once())->method('flush');

        $user = new User();
        $car = new Car();

        $reservation = $this->service->create(
            $user,
            $car,
            new \DateTime('2026-05-01'),
            new \DateTime('2026-05-05')
        );

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertSame($user, $reservation->getUser());
        $this->assertSame($car, $reservation->getCar());
    }


    public function testCreateReservationWithInvalidDates(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = new User();
        $car = new Car();

        $this->service->create(
            $user,
            $car,
            new \DateTime('2026-05-05'),
            new \DateTime('2026-05-01')
        );
    }

    public function testCreateWithUnavailableCar(): void
    {
        $this->carService->method('isAvailable')->willReturn(false);

        $this->expectException(\DomainException::class);

        $user = new User();
        $car = new Car();

        $this->service->create(
            $user,
            $car,
            new \DateTime('2026-05-01'),
            new \DateTime('2026-05-05')
        );
    }

    public function testUpdateWithConflict(): void
    {
        $this->reservationRepository->method('isCarReservedInPeriod')->willReturn(true);

        $this->expectException(\DomainException::class);

        $reservation = new Reservation();
        $car = new Car();

        $reservation->setCar($car);
        $reservation->setStartDate(new \DateTime('2026-05-01'));
        $reservation->setEndDate(new \DateTime('2026-05-05'));

        $this->service->update(
            $reservation,
            new \DateTime('2026-05-03'),
            new \DateTime('2026-05-06')
        );
    }
}