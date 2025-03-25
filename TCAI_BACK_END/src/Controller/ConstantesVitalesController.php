<?php

namespace App\Controller;

use App\Entity\ConstantesVitales;
use App\Form\ConstantesVitalesType;
use App\Repository\ConstantesVitalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/constantes/vitales')]
final class ConstantesVitalesController extends AbstractController
{
    #[Route(name: 'app_constantes_vitales_index', methods: ['GET'])]
    public function index(ConstantesVitalesRepository $constantesVitalesRepository): Response
    {
        return $this->render('constantes_vitales/index.html.twig', [
            'constantes_vitales' => $constantesVitalesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_constantes_vitales_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $constantesVitale = new ConstantesVitales();
        $form = $this->createForm(ConstantesVitalesType::class, $constantesVitale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($constantesVitale);
            $entityManager->flush();

            return $this->redirectToRoute('app_constantes_vitales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('constantes_vitales/new.html.twig', [
            'constantes_vitale' => $constantesVitale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constantes_vitales_show', methods: ['GET'])]
    public function show(ConstantesVitales $constantesVitale): Response
    {
        return $this->render('constantes_vitales/show.html.twig', [
            'constantes_vitale' => $constantesVitale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_constantes_vitales_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConstantesVitales $constantesVitale, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConstantesVitalesType::class, $constantesVitale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_constantes_vitales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('constantes_vitales/edit.html.twig', [
            'constantes_vitale' => $constantesVitale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constantes_vitales_delete', methods: ['POST'])]
    public function delete(Request $request, ConstantesVitales $constantesVitale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$constantesVitale->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($constantesVitale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_constantes_vitales_index', [], Response::HTTP_SEE_OTHER);
    }
}
