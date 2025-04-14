<?php

namespace App\Controller;

use App\Entity\DietaHasTipoDieta;
use App\Form\DietaHasTipoDietaType;
use App\Repository\DietaHasTipoDietaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dieta/has/tipo/dieta')]
final class DietaHasTipoDietaController extends AbstractController{
    #[Route(name: 'app_dieta_has_tipo_dieta_index', methods: ['GET'])]
    public function index(DietaHasTipoDietaRepository $dietaHasTipoDietaRepository): Response
    {
        return $this->render('dieta_has_tipo_dieta/index.html.twig', [
            'dieta_has_tipo_dietas' => $dietaHasTipoDietaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dieta_has_tipo_dieta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dietaHasTipoDietum = new DietaHasTipoDieta();
        $form = $this->createForm(DietaHasTipoDietaType::class, $dietaHasTipoDietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dietaHasTipoDietum);
            $entityManager->flush();

            return $this->redirectToRoute('app_dieta_has_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dieta_has_tipo_dieta/new.html.twig', [
            'dieta_has_tipo_dietum' => $dietaHasTipoDietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dieta_has_tipo_dieta_show', methods: ['GET'])]
    public function show(DietaHasTipoDieta $dietaHasTipoDietum): Response
    {
        return $this->render('dieta_has_tipo_dieta/show.html.twig', [
            'dieta_has_tipo_dietum' => $dietaHasTipoDietum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dieta_has_tipo_dieta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DietaHasTipoDieta $dietaHasTipoDietum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DietaHasTipoDietaType::class, $dietaHasTipoDietum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dieta_has_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dieta_has_tipo_dieta/edit.html.twig', [
            'dieta_has_tipo_dietum' => $dietaHasTipoDietum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dieta_has_tipo_dieta_delete', methods: ['POST'])]
    public function delete(Request $request, DietaHasTipoDieta $dietaHasTipoDietum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dietaHasTipoDietum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dietaHasTipoDietum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dieta_has_tipo_dieta_index', [], Response::HTTP_SEE_OTHER);
    }
}
