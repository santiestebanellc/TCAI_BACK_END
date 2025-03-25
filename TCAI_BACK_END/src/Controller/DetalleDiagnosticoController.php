<?php

namespace App\Controller;

use App\Entity\DetalleDiagnostico;
use App\Form\DetalleDiagnosticoType;
use App\Repository\DetalleDiagnosticoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/detalle/diagnostico')]
final class DetalleDiagnosticoController extends AbstractController
{
    #[Route(name: 'app_detalle_diagnostico_index', methods: ['GET'])]
    public function index(DetalleDiagnosticoRepository $detalleDiagnosticoRepository): Response
    {
        return $this->render('detalle_diagnostico/index.html.twig', [
            'detalle_diagnosticos' => $detalleDiagnosticoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detalle_diagnostico_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $detalleDiagnostico = new DetalleDiagnostico();
        $form = $this->createForm(DetalleDiagnosticoType::class, $detalleDiagnostico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($detalleDiagnostico);
            $entityManager->flush();

            return $this->redirectToRoute('app_detalle_diagnostico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detalle_diagnostico/new.html.twig', [
            'detalle_diagnostico' => $detalleDiagnostico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detalle_diagnostico_show', methods: ['GET'])]
    public function show(DetalleDiagnostico $detalleDiagnostico): Response
    {
        return $this->render('detalle_diagnostico/show.html.twig', [
            'detalle_diagnostico' => $detalleDiagnostico,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detalle_diagnostico_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DetalleDiagnostico $detalleDiagnostico, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DetalleDiagnosticoType::class, $detalleDiagnostico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_detalle_diagnostico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('detalle_diagnostico/edit.html.twig', [
            'detalle_diagnostico' => $detalleDiagnostico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detalle_diagnostico_delete', methods: ['POST'])]
    public function delete(Request $request, DetalleDiagnostico $detalleDiagnostico, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$detalleDiagnostico->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($detalleDiagnostico);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_detalle_diagnostico_index', [], Response::HTTP_SEE_OTHER);
    }
}
