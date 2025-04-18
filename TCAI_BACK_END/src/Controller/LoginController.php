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
        try {
            $data = json_decode($request->getContent(), true);
    
            if (!is_array($data)) {
                return $this->json(['success' => false, 'error' => 'Petición inválida. JSON malformado.'], 400);
            }
    
            $numTrabajador = $data['num_trabajador'] ?? null;
            $password = $data['contraseña'] ?? null;
    
            if (empty($numTrabajador) || empty($password)) {
                return $this->json(['success' => false, 'error' => 'Faltan credenciales obligatorias.'], 400);
            }
    
            // Buscar al auxiliar por número de trabajador
            $auxiliar = $repo->findOneBy(['num_trabajador' => $numTrabajador]);
    
            if (!$auxiliar) {
                return $this->json(['success' => false, 'error' => 'Usuario no encontrado.'], 401);
            }
    
            // Comparar contraseñas (nota: sería mejor usar password_hash y password_verify)
            if ($auxiliar->getContraseña() !== $password) {
                return $this->json(['success' => false, 'error' => 'Contraseña incorrecta.'], 401);
            }
    
            // Respuesta correcta con datos del auxiliar
            return $this->json([
                'success' => true,
                'message' => 'Login correcto.',
                'auxiliar' => [
                    'id' => $auxiliar->getId(),
                    'num_trabajador' => $auxiliar->getNumTrabajador(),
                    'nombre' => $auxiliar->getNombre(),
                    'apellidos' => $auxiliar->getApellidos(),
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
