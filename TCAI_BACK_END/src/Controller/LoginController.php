<?php

namespace App\Controller;

use App\Repository\AuxiliarRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, AuxiliarRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $numTrabajador = $data['num_trabajador'] ?? null;
        $password = $data['contraseña'] ?? null;

        if (!$numTrabajador || !$password) {
            return $this->json(['success' => false, 'error' => 'Missing credentials'], 400);
        }

        $auxiliar = $repo->findOneBy(['num_trabajador' => $numTrabajador]);

        if (!$auxiliar) {
            return $this->json(['success' => false, 'error' => 'User not found'], 401);
        }

        if ($auxiliar->getContraseña() !== $password) {
            return $this->json(['success' => false, 'error' => 'Incorrect password'], 401);
        }

        return $this->json([
            'success' => true,
            'message' => 'Login correct',
            'auxiliar' => [
                'id' => $auxiliar->getId(),
                'num_trabajador' => $auxiliar->getNumTrabajador(),
                'nombre' => $auxiliar->getNombre(),
                'apellidos' => $auxiliar->getApellidos(),
            ]
        ]);
    }
}
