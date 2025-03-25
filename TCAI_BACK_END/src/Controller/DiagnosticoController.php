<?php

namespace App\Controller;

use App\Entity\Diagnostico;
use App\Form\DiagnosticoType;
use App\Repository\DiagnosticoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/diagnostico')]
final class DiagnosticoController extends AbstractController
{
    #[Route(name: 'app_diagnostico_index', methods: ['GET'])]
    public function index(DiagnosticoRepository $diagnosticoRepository): Response
    {
        return $this->render('diagnostico/index.html.twig', [
            'diagnosticos' => $diagnosticoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_diagnostico_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $diagnostico = new Diagnostico();
        $form = $this->createForm(DiagnosticoType::class, $diagnostico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($diagnostico);
            $entityManager->flush();

            return $this->redirectToRoute('app_diagnostico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('diagnostico/new.html.twig', [
            'diagnostico' => $diagnostico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_diagnostico_show', methods: ['GET'])]
    public function show(Diagnostico $diagnostico): Response
    {
        return $this->render('diagnostico/show.html.twig', [
            'diagnostico' => $diagnostico,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_diagnostico_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Diagnostico $diagnostico, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DiagnosticoType::class, $diagnostico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_diagnostico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('diagnostico/edit.html.twig', [
            'diagnostico' => $diagnostico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_diagnostico_delete', methods: ['POST'])]
    public function delete(Request $request, Diagnostico $diagnostico, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diagnostico->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($diagnostico);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_diagnostico_index', [], Response::HTTP_SEE_OTHER);
    }
}
