<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Form\RegistroType;
use App\Repository\RegistroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\PacienteRepository;


#[Route('/registro')]
final class RegistroController extends AbstractController{
    #[Route(name: 'app_registro_index', methods: ['GET'])]
    public function index(RegistroRepository $registroRepository): Response
    {
        return $this->render('registro/index.html.twig', [
            'registros' => $registroRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_registro_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $registro = new Registro();
        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($registro);
            $entityManager->flush();

            return $this->redirectToRoute('app_registro_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registro/new.html.twig', [
            'registro' => $registro,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_registro_show', methods: ['GET'])]
    public function show(Registro $registro): Response
    {
        return $this->render('registro/show.html.twig', [
            'registro' => $registro,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_registro_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Registro $registro, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_registro_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registro/edit.html.twig', [
            'registro' => $registro,
            'form' => $form,
        ]);
    }

    #[Route('/paciente/{id}', name: 'api_registros_by_paciente', methods: ['GET'])]
    public function getRegistrosByPaciente(int $id, RegistroRepository $registroRepository, PacienteRepository $pacienteRepository): JsonResponse
    {
    try {
        // Buscar el paciente por ID
        $paciente = $pacienteRepository->find($id);

        // Verificar si el paciente existe
        if (!$paciente) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'Paciente no encontrado'
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        // Obtener todos los registros asociados al paciente
        $registros = $registroRepository->findBy(['paciente_id' => $paciente]);

        // Si no se encuentran registros, devolver un mensaje apropiado
        if (empty($registros)) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'No se encontraron registros para este paciente'
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        // Formatear los resultados para la respuesta JSON
        $formattedResults = [];
        foreach ($registros as $registro) {
            $formattedResults[] = [
                'registro_id' => $registro->getId(),
                'registro' => [
                    'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                    'toma' => $registro->getToma(),
                    'nombre_auxiliar' => $registro->getAuxiliarId()->getNombre(),
                    'numero_auxiliar' => $registro->getAuxiliarId()->getNumTrabajador(),
                    'observacion' => $registro->getObservacion()->getDescripcion()
                ]
            ];
        }

        return new JsonResponse([
            'success' => true,
            'content' => $formattedResults
        ], Response::HTTP_OK);

    } catch (\Exception $e) {
        return new JsonResponse([
            'success' => false,
            'content' => [
                'message' => 'Error interno: ' . $e->getMessage()
            ]
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}