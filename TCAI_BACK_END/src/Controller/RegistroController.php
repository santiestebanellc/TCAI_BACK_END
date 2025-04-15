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

    #[Route('/{id}', name: 'app_registro_delete', methods: ['POST'])]
    public function delete(Request $request, Registro $registro, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registro->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($registro);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_registro_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/api/registros/paciente', name: 'get_registros_por_paciente', methods: ['POST'])]
    public function getRegistrosPorPaciente(Request $request, RegistroRepository $registroRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['paciente_id'])) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'Falta el campo paciente_id'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $pacienteId = $data['paciente_id'];

        $registros = $registroRepository->findBy(['paciente' => $pacienteId]);

        if (!$registros) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'No se encontraron registros para este paciente'
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        $contenido = [];

        foreach ($registros as $registro) {
            $contenido[] = [
                'registro_id' => $registro->getId(),
                'registro' => [
                    'fecha' => $registro->getFecha()->format('Y-m-d H:i:s'),
                    'toma' => $registro->getToma(),
                    'nombre_auxiliar' => $registro->getNombreAuxiliar(),
                    'numero_auxiliar' => $registro->getNumeroAuxiliar(),
                    'observacion' => $registro->getObservacion()
                ]
            ];
        }

        return new JsonResponse([
            'success' => true,
            'content' => $contenido
        ], Response::HTTP_OK);
    }
    


}
