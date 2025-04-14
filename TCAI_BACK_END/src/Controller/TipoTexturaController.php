<?php

namespace App\Controller;

use App\Entity\TipoTextura;
use App\Form\TipoTexturaType;
use App\Repository\TipoTexturaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tipo/textura')]
final class TipoTexturaController extends AbstractController{
    #[Route(name: 'app_tipo_textura_index', methods: ['GET'])]
    public function index(TipoTexturaRepository $tipoTexturaRepository): Response
    {
        return $this->render('tipo_textura/index.html.twig', [
            'tipo_texturas' => $tipoTexturaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tipo_textura_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoTextura = new TipoTextura();
        $form = $this->createForm(TipoTexturaType::class, $tipoTextura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoTextura);
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_textura_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_textura/new.html.twig', [
            'tipo_textura' => $tipoTextura,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_textura_show', methods: ['GET'])]
    public function show(TipoTextura $tipoTextura): Response
    {
        return $this->render('tipo_textura/show.html.twig', [
            'tipo_textura' => $tipoTextura,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tipo_textura_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TipoTextura $tipoTextura, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TipoTexturaType::class, $tipoTextura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tipo_textura_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tipo_textura/edit.html.twig', [
            'tipo_textura' => $tipoTextura,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tipo_textura_delete', methods: ['POST'])]
    public function delete(Request $request, TipoTextura $tipoTextura, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoTextura->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tipoTextura);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tipo_textura_index', [], Response::HTTP_SEE_OTHER);
    }
}
