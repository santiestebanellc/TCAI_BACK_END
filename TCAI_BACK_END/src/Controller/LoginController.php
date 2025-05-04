<?php

namespace App\Controller;

use App\Repository\AuxiliarRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST', 'OPTIONS'])]
    public function login(Request $request, AuxiliarRepository $repo): Response
    {
        // Si es una solicitud OPTIONS, responde inmediatamente con cabeceras CORS
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:4200');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '3600');
            return $response;
        }

        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json(['success' => false, 'error' => 'Petición inválida. JSON malformado.'], 400, [
                    'Access-Control-Allow-Origin' => 'http://localhost:4200',
                    'Access-Control-Allow-Credentials' => 'true'
                ]);
            }

            $numTrabajador = $data['num_trabajador'] ?? null;
            $password = $data['contrasena'] ?? null;

            if (empty($numTrabajador) || empty($password)) {
                return $this->json(['success' => false, 'error' => 'Faltan credenciales obligatorias.'], 400, [
                    'Access-Control-Allow-Origin' => 'http://localhost:4200',
                    'Access-Control-Allow-Credentials' => 'true'
                ]);
            }

            // Buscar al auxiliar por número de trabajador
            $auxiliar = $repo->findOneBy(['num_trabajador' => $numTrabajador]);

            if (!$auxiliar) {
                return $this->json(['success' => false, 'error' => 'Usuario no encontrado.'], 401, [
                    'Access-Control-Allow-Origin' => 'http://localhost:4200',
                    'Access-Control-Allow-Credentials' => 'true'
                ]);
            }

            // Comparar contraseñas (nota: sería mejor usar password_hash y password_verify)
            if ($auxiliar->getContraseña() !== $password) {
                return $this->json(['success' => false, 'error' => 'Contraseña incorrecta.'], 401, [
                    'Access-Control-Allow-Origin' => 'http://localhost:4200',
                    'Access-Control-Allow-Credentials' => 'true'
                ]);
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
            ], 200, [
                'Access-Control-Allow-Origin' => 'http://localhost:4200',
                'Access-Control-Allow-Credentials' => 'true'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500, [
                'Access-Control-Allow-Origin' => 'http://localhost:4200',
                'Access-Control-Allow-Credentials' => 'true'
            ]);
        }
    }
}
