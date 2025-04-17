<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\DetalleDiagnostico;
use App\Form\DetalleDiagnosticoType;
use App\Repository\DetalleDiagnosticoRepository;
use App\Repository\DiagnosticoRepository;
use App\Repository\HabitacionRepository;
use App\Repository\PacienteRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class HospitalDataController extends AbstractController
{
    #[Route('/hospital/data', name: 'app_hospital_data')]
    public function index(): Response
    {
        return $this->render('hospital_data/index.html.twig', [
            'controller_name' => 'HospitalDataController',
        ]);
    }

    #[Route('/diagnostico/paciente/{id}', name: 'api_diagnostico_by_paciente', methods: ['GET'])]
    public function summaryShow(int $id, DetalleDiagnosticoRepository $detalleDiagnosticoRepository, DiagnosticoRepository $diagnosticoRepository, PacienteRepository $pacienteRepository): JsonResponse
    {
        try {
            // REVISAR VALIDACION !!!
            // if (!$codigo) {
            //     return $this->json(
            //         ['success' => false, 'content' => ['message' => 'Room code is required']],
            //         Response::HTTP_BAD_REQUEST
            //     );
            // }

            // Find the paciente by codigo
            $paciente = $pacienteRepository->find($id);

            // Get all diagnostico entities related to the paciente
            $diagnosticos = $diagnosticoRepository->findBy(['paciente_id' => $paciente]);

            // Create array of all diagnostico IDs 
            $diagnosticoMap = [];
            foreach ($diagnosticos as $diagnostico) {
                $diagnosticoMap[$diagnostico->getId()] = $diagnostico;
            }

            // Extract diagnostico IDs
            $diagnosticoIds = array_keys($diagnosticoMap);

            // Get detalle_diagnostico associated with the diagnostico IDs
            $detalleDiagnosticos = $detalleDiagnosticoRepository->createQueryBuilder('dd')
                ->where('dd.diagnostico_id IN (:diagnosticoIds)')
                ->setParameter('diagnosticoIds', $diagnosticoIds)
                ->getQuery()
                ->getResult();

            $formattedResults = [];

            foreach ($detalleDiagnosticos as $detalleDiagnostico) {
                $diagnostico = $detalleDiagnostico->getDiagnosticoId();
                $formattedResults[] = [
                    'diagnostico_id' => $diagnostico->getId(),
                    'detalle_diagnostico' => [
                        'fecha' => $diagnostico->getFecha() ? $diagnostico->getFecha()->format('Y-m-d H:i:s') : null,
                        'toma' => $diagnostico->getToma(),
                        'nombre_auxiliar' => $diagnostico->getAuxiliarId()->getNombre(),
                        'numero_auxiliar' => $diagnostico->getAuxiliarId()->getNumTrabajador(),
                        'avd' => $detalleDiagnostico->getAvd(),
                        'o2' => $detalleDiagnostico->getO2(),
                        'panales' => $detalleDiagnostico->getPanales()
                    ]
                ];
            }

            return $this->json(
                ['success' => true, 'content' => $formattedResults],
                Response::HTTP_OK,
                []
            );
        } catch (\Exception $e) {
            return $this->json(
                ['success' => false, 'content' => ['error' => $e->getMessage()]],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}