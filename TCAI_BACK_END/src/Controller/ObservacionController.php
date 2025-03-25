<?php

namespace App\Controller;

use App\Entity\Observacion;
use App\Form\ObservacionType;
use App\Repository\ObservacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/observacion')]
final class ObservacionController extends AbstractController
{
    #[Route(name: 'app_observacion_index', methods: ['GET'])]
    public function index(ObservacionRepository $observacionRepository): Response
    {
        return $this->render('observacion/index.html.twig', [
            'observacions' => $observacionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_observacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $observacion = new Observacion();
        $form = $this->createForm(ObservacionType::class, $observacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($observacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_observacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('observacion/new.html.twig', [
            'observacion' => $observacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_observacion_show', methods: ['GET'])]
    public function show(Observacion $observacion): Response
    {
        return $this->render('observacion/show.html.twig', [
            'observacion' => $observacion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_observacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Observacion $observacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ObservacionType::class, $observacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_observacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('observacion/edit.html.twig', [
            'observacion' => $observacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_observacion_delete', methods: ['POST'])]
    public function delete(Request $request, Observacion $observacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$observacion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($observacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_observacion_index', [], Response::HTTP_SEE_OTHER);
    }
}
