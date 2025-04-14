<?php

namespace App\Controller;

use App\Entity\Sueroterapia;
use App\Form\SueroterapiaType;
use App\Repository\SueroterapiaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sueroterapia')]
final class SueroterapiaController extends AbstractController{
    #[Route(name: 'app_sueroterapia_index', methods: ['GET'])]
    public function index(SueroterapiaRepository $sueroterapiaRepository): Response
    {
        return $this->render('sueroterapia/index.html.twig', [
            'sueroterapias' => $sueroterapiaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sueroterapia_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sueroterapium = new Sueroterapia();
        $form = $this->createForm(SueroterapiaType::class, $sueroterapium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sueroterapium);
            $entityManager->flush();

            return $this->redirectToRoute('app_sueroterapia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sueroterapia/new.html.twig', [
            'sueroterapium' => $sueroterapium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sueroterapia_show', methods: ['GET'])]
    public function show(Sueroterapia $sueroterapium): Response
    {
        return $this->render('sueroterapia/show.html.twig', [
            'sueroterapium' => $sueroterapium,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sueroterapia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sueroterapia $sueroterapium, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SueroterapiaType::class, $sueroterapium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sueroterapia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sueroterapia/edit.html.twig', [
            'sueroterapium' => $sueroterapium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sueroterapia_delete', methods: ['POST'])]
    public function delete(Request $request, Sueroterapia $sueroterapium, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sueroterapium->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sueroterapium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sueroterapia_index', [], Response::HTTP_SEE_OTHER);
    }
}
