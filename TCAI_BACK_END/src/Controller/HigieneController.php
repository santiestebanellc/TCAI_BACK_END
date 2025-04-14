<?php

namespace App\Controller;

use App\Entity\Higiene;
use App\Form\HigieneType;
use App\Repository\HigieneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/higiene')]
final class HigieneController extends AbstractController{
    #[Route(name: 'app_higiene_index', methods: ['GET'])]
    public function index(HigieneRepository $higieneRepository): Response
    {
        return $this->render('higiene/index.html.twig', [
            'higienes' => $higieneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_higiene_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $higiene = new Higiene();
        $form = $this->createForm(HigieneType::class, $higiene);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($higiene);
            $entityManager->flush();

            return $this->redirectToRoute('app_higiene_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('higiene/new.html.twig', [
            'higiene' => $higiene,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_higiene_show', methods: ['GET'])]
    public function show(Higiene $higiene): Response
    {
        return $this->render('higiene/show.html.twig', [
            'higiene' => $higiene,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_higiene_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Higiene $higiene, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HigieneType::class, $higiene);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_higiene_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('higiene/edit.html.twig', [
            'higiene' => $higiene,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_higiene_delete', methods: ['POST'])]
    public function delete(Request $request, Higiene $higiene, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$higiene->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($higiene);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_higiene_index', [], Response::HTTP_SEE_OTHER);
    }
}
