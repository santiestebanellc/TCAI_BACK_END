<?php

namespace App\Controller;

use App\Entity\TipoHigiene;
use App\Form\TipoHigieneType;
use App\Repository\TipoHigieneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipo/higiene')]
final class TipoHigieneController extends AbstractController{
    #[Route(name: 'app_tipo_higiene_index', methods: ['GET'])]
    public function index(TipoHigieneRepository $tipoHigieneRepository): Response
    {
        return $this->render('tipo_higiene/index.html.twig', [
            'tipo_higienes' => $tipoHigieneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tipo_higiene_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoHigiene = new TipoHigiene();
        $form = $this->createForm(TipoHigieneType::class, $tipoHigiene);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoHigiene);
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_higiene_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_higiene/new.html.twig', [
            'tipo_higiene' => $tipoHigiene,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_higiene_show', methods: ['GET'])]
    public function show(TipoHigiene $tipoHigiene): Response
    {
        return $this->render('tipo_higiene/show.html.twig', [
            'tipo_higiene' => $tipoHigiene,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tipo_higiene_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoHigiene $tipoHigiene, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TipoHigieneType::class, $tipoHigiene);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_higiene_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_higiene/edit.html.twig', [
            'tipo_higiene' => $tipoHigiene,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_higiene_delete', methods: ['POST'])]
    public function delete(Request $request, TipoHigiene $tipoHigiene, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoHigiene->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tipoHigiene);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tipo_higiene_index', [], Response::HTTP_SEE_OTHER);
    }
}
