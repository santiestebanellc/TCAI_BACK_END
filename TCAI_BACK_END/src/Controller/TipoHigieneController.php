<?php

namespace App\Controller;

use App\Repository\TipoHigieneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class TipoHigieneController extends AbstractController
{
    #[Route(path: 'tipo/higiene', name: 'app_tipo_higiene_index', methods: ['GET'])]
    public function getTipoHigiene(TipoHigieneRepository $tipoHigieneRepository): JsonResponse
    {
        try {
            $tiposHigiene = $tipoHigieneRepository->findAll();

            return $this->json([
                'success' => true,
                'content' => $tiposHigiene
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
