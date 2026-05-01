<?php

namespace App\Controller;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class CarController extends AbstractController
{
    private CarRepository $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    //liste des voitures
    #[Route('/cars', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $cars = $this->carRepository->getAllCars();

        return $this->json($cars);
    }

    //détail d'une voiture spécific
    #[Route('/cars/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $car = $this->carRepository->getCarById($id);

        if (!$car) {
            return $this->json(['message' => 'Car not found'],Response::HTTP_NOT_FOUND);
        }

        return $this->json($car);    
    }
}
