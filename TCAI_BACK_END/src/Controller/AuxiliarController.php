<?php

namespace App\Controller;

use App\Entity\Auxiliar;
use App\Form\AuxiliarType;
use App\Repository\AuxiliarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auxiliar')]
final class AuxiliarController extends AbstractController
{
    #[Route(name: 'app_auxiliar_index', methods: ['GET'])]
    public function index(AuxiliarRepository $auxiliarRepository): JsonResponse
    {
        try {
            $auxiliars = $auxiliarRepository->findAll();
            return $this->json(
                ['success' => true, 'content' => $auxiliars],
                Response::HTTP_OK,
                [],
                ['groups' => 'auxiliar:read']
            );
        } catch (\Exception $e) {
            return $this->json(
                ['success' => false, 'content' => ['error' => $e->getMessage()]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/new', name: 'app_auxiliar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapRequestPayload] Auxiliar $auxiliar): JsonResponse
    {
        if ($request->getMethod() === 'GET') {
            return $this->json(
                ['success' => false, 'content' => ['error' => 'Method not allowed']],
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        }

        try {
            // Obtener el num_trabajador del auxiliar
            $numTrabajador = $auxiliar->getNumTrabajador();

            // Si num_trabajador no es null, verificar si ya existe en la tabla
            if ($numTrabajador !== null) {
                // Usar una consulta SQL directa para verificar duplicados
                $connection = $entityManager->getConnection();
                $sql = 'SELECT COUNT(*) FROM auxiliar WHERE num_trabajador = :num_trabajador';
                $stmt = $connection->prepare($sql);
                $stmt->bindValue('num_trabajador', $numTrabajador);
                $result = $stmt->executeQuery();
                $count = $result->fetchNumeric()[0]; // fetchNumeric() devuelve un array, tomamos el primer valor

                if ($count > 0) {
                    return $this->json(
                        ['success' => false, 'content' => ['errors' => ['num_trabajador' => 'El número de trabajador ya existe']]],
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
            }

            // Si no hay duplicados, proceder a guardar
            $entityManager->persist($auxiliar);
            $entityManager->flush();

            return $this->json(
                ['success' => true, 'content' => ['auxiliar' => $auxiliar]],
                Response::HTTP_CREATED,
                [],
                ['groups' => 'auxiliar:read']
            );
        } catch (NotNormalizableValueException $e) {
            return $this->json(
                ['success' => false, 'content' => ['errors' => ['message' => $e->getMessage()]]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (HttpException $e) {
            return $this->json(
                ['success' => false, 'content' => ['errors' => ['message' => $e->getMessage()]]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->json(
                ['success' => false, 'content' => ['error' => $e->getMessage()]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'app_auxiliar_show', methods: ['GET'])]
    public function show(int $id, AuxiliarRepository $auxiliarRepository): JsonResponse
    {
        try {
            $auxiliar = $auxiliarRepository->find($id);
            if (!$auxiliar) {
                return $this->json(
                    ['success' => false, 'content' => ['error' => 'Auxiliar not found']],
                    Response::HTTP_NOT_FOUND
                );
            }
            return $this->json(
                ['success' => true, 'content' => ['auxiliar' => $auxiliar]],
                Response::HTTP_OK,
                [],
                ['groups' => 'auxiliar:read']
            );
        } catch (\Exception $e) {
            return $this->json(
                ['success' => false, 'content' => ['error' => $e->getMessage()]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


}
