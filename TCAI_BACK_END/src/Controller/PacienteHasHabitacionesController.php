<?php

namespace App\Controller;

use App\Entity\PacienteHasHabitaciones;
use App\Form\PacienteHasHabitacionesType;
use App\Repository\PacienteHasHabitacionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/paciente/has/habitaciones')]
final class PacienteHasHabitacionesController extends AbstractController{
    #[Route(name: 'app_paciente_has_habitaciones_index', methods: ['GET'])]
    public function index(PacienteHasHabitacionesRepository $pacienteHasHabitacionesRepository): Response
    {
        return $this->render('paciente_has_habitaciones/index.html.twig', [
            'paciente_has_habitaciones' => $pacienteHasHabitacionesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_paciente_has_habitaciones_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pacienteHasHabitacione = new PacienteHasHabitaciones();
        $form = $this->createForm(PacienteHasHabitacionesType::class, $pacienteHasHabitacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pacienteHasHabitacione);
            $entityManager->flush();

            return $this->redirectToRoute('app_paciente_has_habitaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paciente_has_habitaciones/new.html.twig', [
            'paciente_has_habitacione' => $pacienteHasHabitacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paciente_has_habitaciones_show', methods: ['GET'])]
    public function show(PacienteHasHabitaciones $pacienteHasHabitacione): Response
    {
        return $this->render('paciente_has_habitaciones/show.html.twig', [
            'paciente_has_habitacione' => $pacienteHasHabitacione,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paciente_has_habitaciones_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PacienteHasHabitaciones $pacienteHasHabitacione, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PacienteHasHabitacionesType::class, $pacienteHasHabitacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_paciente_has_habitaciones_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paciente_has_habitaciones/edit.html.twig', [
            'paciente_has_habitacione' => $pacienteHasHabitacione,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paciente_has_habitaciones_delete', methods: ['POST'])]
    public function delete(Request $request, PacienteHasHabitaciones $pacienteHasHabitacione, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pacienteHasHabitacione->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pacienteHasHabitacione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_paciente_has_habitaciones_index', [], Response::HTTP_SEE_OTHER);
    }
}
