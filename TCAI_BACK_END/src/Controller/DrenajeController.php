<?php

namespace App\Controller;

use App\Entity\Drenaje;
use App\Form\DrenajeType;
use App\Repository\DrenajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/drenaje')]
final class DrenajeController extends AbstractController{
    #[Route(name: 'app_drenaje_index', methods: ['GET'])]
    public function index(DrenajeRepository $drenajeRepository): Response
    {
        return $this->render('drenaje/index.html.twig', [
            'drenajes' => $drenajeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_drenaje_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $drenaje = new Drenaje();
        $form = $this->createForm(DrenajeType::class, $drenaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($drenaje);
            $entityManager->flush();

            return $this->redirectToRoute('app_drenaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('drenaje/new.html.twig', [
            'drenaje' => $drenaje,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_drenaje_show', methods: ['GET'])]
    public function show(Drenaje $drenaje): Response
    {
        return $this->render('drenaje/show.html.twig', [
            'drenaje' => $drenaje,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_drenaje_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Drenaje $drenaje, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DrenajeType::class, $drenaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_drenaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('drenaje/edit.html.twig', [
            'drenaje' => $drenaje,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_drenaje_delete', methods: ['POST'])]
    public function delete(Request $request, Drenaje $drenaje, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$drenaje->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($drenaje);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_drenaje_index', [], Response::HTTP_SEE_OTHER);
    }
}
