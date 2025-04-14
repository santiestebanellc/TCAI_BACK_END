<?php

namespace App\Controller;

use App\Entity\BalanceHidrico;
use App\Form\BalanceHidricoType;
use App\Repository\BalanceHidricoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/balance/hidrico')]
final class BalanceHidricoController extends AbstractController{
    #[Route(name: 'app_balance_hidrico_index', methods: ['GET'])]
    public function index(BalanceHidricoRepository $balanceHidricoRepository): Response
    {
        return $this->render('balance_hidrico/index.html.twig', [
            'balance_hidricos' => $balanceHidricoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_balance_hidrico_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $balanceHidrico = new BalanceHidrico();
        $form = $this->createForm(BalanceHidricoType::class, $balanceHidrico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($balanceHidrico);
            $entityManager->flush();

            return $this->redirectToRoute('app_balance_hidrico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('balance_hidrico/new.html.twig', [
            'balance_hidrico' => $balanceHidrico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_balance_hidrico_show', methods: ['GET'])]
    public function show(BalanceHidrico $balanceHidrico): Response
    {
        return $this->render('balance_hidrico/show.html.twig', [
            'balance_hidrico' => $balanceHidrico,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_balance_hidrico_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BalanceHidrico $balanceHidrico, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BalanceHidricoType::class, $balanceHidrico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_balance_hidrico_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('balance_hidrico/edit.html.twig', [
            'balance_hidrico' => $balanceHidrico,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_balance_hidrico_delete', methods: ['POST'])]
    public function delete(Request $request, BalanceHidrico $balanceHidrico, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$balanceHidrico->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($balanceHidrico);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_balance_hidrico_index', [], Response::HTTP_SEE_OTHER);
    }
}
