<?php

namespace App\Controller;

use App\Entity\Habitacion;
use App\Form\HabitacionType;
use App\Repository\HabitacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use function PHPUnit\Framework\isEmpty;

#[Route('/habitacion')]
final class HabitacionController extends AbstractController
{
    #[Route('/general', name: 'app_habitacion_index', methods: ['GET'])]
    public function index(HabitacionRepository $habitacionRepository): JsonResponse
    {
        $habitaciones = $habitacionRepository->findAll();

        if (!empty($habitaciones)) {

            foreach ($habitaciones as $habitacion) {
                $habitacionInfo = [
                    'habitacion_codigo' => $habitacion->getCodigo(),
                ];

                $pacientesRelacionados = $habitacion->getPacienteHasHabitaciones();

                if ($pacientesRelacionados->isEmpty()) {
                    $habitacionInfo['isEmpty'] = true;
                } else {
                    $habitacionInfo['isEmpty'] = false;

                    $paciente = $pacientesRelacionados->last()?->getPacienteId();

                    if ($paciente) {
                        $registros = $paciente->getRegistros()->toArray();

                        usort($registros, function ($a, $b) {
                            return $b->getFecha() <=> $a->getFecha();
                        });

                        // dd($registros);
                        $ultimoRegistro = $registros[0] ?? null;

                        $habitacionInfo['paciente'] = [
                            'nombre' => $paciente->getNombre(),
                            'apellidos' => $paciente->getApellidos(),
                            'edad' => $paciente->getFechaNacimiento() /* cambiar por calcular la edad*/,
                            'diagnostico' => $paciente->getDiagnostico()->last()->getDiagnostico(),
                        ];

                        if ($ultimoRegistro) {
                            $habitacionInfo['registro'] = [
                                'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                                'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                                'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                                'observaciones' => $ultimoRegistro->getObservacion()->getDescripcion(),
                                'alerta' => true/*arreglar mas tarde*/,
                            ];
                        } else {
                            $habitacionInfo['registro'] = null;
                        }
                    }
                }

                $data[] = $habitacionInfo;
            }

            return $this->json([
                'success' => true,
                'message' => 'List rooms correct',
                'habitacion' => $data,
            ]);
        } else {
            return $this->json([
                'success' => false,
                'message' => 'There are no rooms',
                'habitacion' => [],
            ]);
        }
    }

    #[Route('/new', name: 'app_habitacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $habitacion = new Habitacion();
        $form = $this->createForm(HabitacionType::class, $habitacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($habitacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_habitacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('habitacion/new.html.twig', [
            'habitacion' => $habitacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_habitacion_show', methods: ['GET'])]
    public function show(Habitacion $habitacion): Response
    {
        return $this->render('habitacion/show.html.twig', [
            'habitacion' => $habitacion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_habitacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Habitacion $habitacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HabitacionType::class, $habitacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_habitacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('habitacion/edit.html.twig', [
            'habitacion' => $habitacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_habitacion_delete', methods: ['POST'])]
    public function delete(Request $request, Habitacion $habitacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $habitacion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($habitacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_habitacion_index', [], Response::HTTP_SEE_OTHER);
    }
}
