<?php

namespace App\Controller;

use App\Repository\TipoTexturaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class TipoTexturaController extends AbstractController
{
    #[Route(path: '/tipo/textura', name: 'app_tipo_textura_index', methods: ['GET'])]
    public function getTipoTextura(TipoTexturaRepository $tipoTexturaRepository): JsonResponse
    {
        try {
            $tiposTextura = $tipoTexturaRepository->findAll();

            return $this->json([
                'success' => true,
                'content' => $tiposTextura
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
