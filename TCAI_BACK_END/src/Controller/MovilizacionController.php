<?php

namespace App\Controller;

use App\Entity\Movilizacion;
use App\Form\MovilizacionType;
use App\Repository\MovilizacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/movilizacion')]
final class MovilizacionController extends AbstractController{
    #[Route(name: 'app_movilizacion_index', methods: ['GET'])]
    public function index(MovilizacionRepository $movilizacionRepository): Response
    {
        return $this->render('movilizacion/index.html.twig', [
            'movilizacions' => $movilizacionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_movilizacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $movilizacion = new Movilizacion();
        $form = $this->createForm(MovilizacionType::class, $movilizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($movilizacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_movilizacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movilizacion/new.html.twig', [
            'movilizacion' => $movilizacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_movilizacion_show', methods: ['GET'])]
    public function show(Movilizacion $movilizacion): Response
    {
        return $this->render('movilizacion/show.html.twig', [
            'movilizacion' => $movilizacion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_movilizacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Movilizacion $movilizacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MovilizacionType::class, $movilizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_movilizacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movilizacion/edit.html.twig', [
            'movilizacion' => $movilizacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_movilizacion_delete', methods: ['POST'])]
    public function delete(Request $request, Movilizacion $movilizacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movilizacion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($movilizacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_movilizacion_index', [], Response::HTTP_SEE_OTHER);
    }
}
