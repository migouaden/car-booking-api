<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Services\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ReservationController extends AbstractController
{
    private ReservationService $reservationService;
    private ReservationRepository $reservationRepository;
    private CarRepository $carRepository;
    private UserRepository $userRepository;


    public function __construct(ReservationService $reservationService ,  ReservationRepository $reservationRepository ,CarRepository $carRepository , UserRepository $userRepository )
    {
        $this->reservationService = $reservationService;
        $this->reservationRepository = $reservationRepository;
        $this->carRepository = $carRepository;
        $this->userRepository = $userRepository;

    }

    #[Route('/reservations', methods: ['POST'])]
    public function create(Request $request): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);

        $currentUser = $this->getUser();

        $car = $this->carRepository->find($data['car_id']);

        if (!$car) {
            return $this->json(['error' => 'Car not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $reservation = $this->reservationService->create(
                $currentUser,
                $car,
                new \DateTime($data['start_date']),
                new \DateTime($data['end_date'])
            );

            return $this->json($this->reservationService->formatReservation($reservation), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/users/{id}/reservations', methods: ['GET'])]
    public function userReservations(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if ($user !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $this->reservationRepository->findReservationsByUser($id);

        return $this->json($reservations);

    }

    #[Route('/reservations/{id}', methods: ['PUT'])]
    public function update(int $id,Request $request): JsonResponse {
        
        $reservation = $this->reservationRepository->find($id);


        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {
            $updated = $this->reservationService->update(
                $reservation,
                new \DateTime($data['start_date']),
                new \DateTime($data['end_date'])
            );

            return $this->json($this->reservationService->formatReservation($updated));

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/reservations/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->reservationService->delete($reservation);

        return $this->json(['message' => 'Reservation Deleted Successfully !']);
    }


}
