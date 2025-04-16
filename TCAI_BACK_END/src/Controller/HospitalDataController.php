<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HospitalDataController extends AbstractController
{
    #[Route('/hospital/data', name: 'app_hospital_data')]
    public function index(): Response
    {
        return $this->render('hospital_data/index.html.twig', [
            'controller_name' => 'HospitalDataController',
        ]);
    }
}
