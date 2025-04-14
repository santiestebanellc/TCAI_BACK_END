<?php

namespace App\Controller;

use App\Entity\TipoDieta;
use App\Form\TipoDietaType;
use App\Repository\TipoDietaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipo/dieta')]
final class TipoDietaController extends AbstractController{
    #[Route(name: 'app_tipo_dieta_index', methods: ['GET'])]
    public function index(TipoDietaRepository $tipoDietaRepository): Response
    {
        return $this->render('tipo_dieta/index.html.twig', [
            'tipo_dietas' => $tipoDietaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tipo_dieta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoDietum = new TipoDieta();
        $form = $this->createForm(TipoDietaType::class, $tipoDietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoDietum);
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_dieta/new.html.twig', [
            'tipo_dietum' => $tipoDietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_dieta_show', methods: ['GET'])]
    public function show(TipoDieta $tipoDietum): Response
    {
        return $this->render('tipo_dieta/show.html.twig', [
            'tipo_dietum' => $tipoDietum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tipo_dieta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoDieta $tipoDietum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TipoDietaType::class, $tipoDietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_dieta/edit.html.twig', [
            'tipo_dietum' => $tipoDietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_dieta_delete', methods: ['POST'])]
    public function delete(Request $request, TipoDieta $tipoDietum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoDietum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tipoDietum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
    }
}
