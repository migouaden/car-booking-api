<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginCheck()
    {

    }

    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    public function apiTest():JsonResponse
    {
        return $this->json(['message'=>'Return this message from controller !']);
    }
}
