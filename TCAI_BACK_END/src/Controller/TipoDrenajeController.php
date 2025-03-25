<?php

namespace App\Controller;

use App\Entity\TipoDrenaje;
use App\Form\TipoDrenajeType;
use App\Repository\TipoDrenajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipo/drenaje')]
final class TipoDrenajeController extends AbstractController
{
    #[Route(name: 'app_tipo_drenaje_index', methods: ['GET'])]
    public function index(TipoDrenajeRepository $tipoDrenajeRepository): Response
    {
        return $this->render('tipo_drenaje/index.html.twig', [
            'tipo_drenajes' => $tipoDrenajeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tipo_drenaje_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoDrenaje = new TipoDrenaje();
        $form = $this->createForm(TipoDrenajeType::class, $tipoDrenaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoDrenaje);
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_drenaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_drenaje/new.html.twig', [
            'tipo_drenaje' => $tipoDrenaje,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_drenaje_show', methods: ['GET'])]
    public function show(TipoDrenaje $tipoDrenaje): Response
    {
        return $this->render('tipo_drenaje/show.html.twig', [
            'tipo_drenaje' => $tipoDrenaje,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tipo_drenaje_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoDrenaje $tipoDrenaje, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TipoDrenajeType::class, $tipoDrenaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_drenaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_drenaje/edit.html.twig', [
            'tipo_drenaje' => $tipoDrenaje,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_drenaje_delete', methods: ['POST'])]
    public function delete(Request $request, TipoDrenaje $tipoDrenaje, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoDrenaje->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tipoDrenaje);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tipo_drenaje_index', [], Response::HTTP_SEE_OTHER);
    }
}
