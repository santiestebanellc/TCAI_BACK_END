<?php

namespace App\Controller;

use App\Entity\Dieta;
use App\Form\DietaType;
use App\Repository\DietaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dieta')]
final class DietaController extends AbstractController
{
    #[Route(name: 'app_dieta_index', methods: ['GET'])]
    public function index(DietaRepository $dietaRepository): Response
    {
        return $this->render('dieta/index.html.twig', [
            'dietas' => $dietaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dieta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dietum = new Dieta();
        $form = $this->createForm(DietaType::class, $dietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dietum);
            $entityManager->flush();

            return $this->redirectToRoute('app_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dieta/new.html.twig', [
            'dietum' => $dietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dieta_show', methods: ['GET'])]
    public function show(Dieta $dietum): Response
    {
        return $this->render('dieta/show.html.twig', [
            'dietum' => $dietum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dieta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dieta $dietum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DietaType::class, $dietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dieta/edit.html.twig', [
            'dietum' => $dietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dieta_delete', methods: ['POST'])]
    public function delete(Request $request, Dieta $dietum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dietum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dietum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dieta_index', [], Response::HTTP_SEE_OTHER);
    }
}
