<?php

namespace App\Controller;

use App\Repository\TipoDietaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TipoDietaController extends AbstractController
{
    #[Route(path: '/tipo/dieta', name: 'app_tipo_dieta_index', methods: ['GET'])]
    public function getTipoDieta(TipoDietaRepository $tipoDietaRepository): JsonResponse
    {
        try {
            $tiposDieta = $tipoDietaRepository->findAll();

            return $this->json([
                'success' => true,
                'content' => $tiposDieta
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
