<?php

namespace App\Controller;

// Others
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Entities
use App\Entity\DetalleDiagnostico;
use App\Entity\Diagnostico;

//Repositories
use App\Repository\AuxiliarRepository;
use App\Repository\DetalleDiagnosticoRepository;
use App\Repository\DiagnosticoRepository;
use App\Repository\PacienteRepository;


final class DiagnosticoController extends AbstractController
{
    // GET
    #[Route('/diagnostico/paciente/{id}', name: 'api_diagnostico_by_paciente', methods: ['GET'])]
    public function getDiagnosticosByPaciente(
        int $id,
        DetalleDiagnosticoRepository $detalleDiagnosticoRepository,
        DiagnosticoRepository $diagnosticoRepository,
        PacienteRepository $pacienteRepository
    ): JsonResponse {
        try {

            // Find the paciente by codigo
            $paciente = $pacienteRepository->find($id);

            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'content' => [
                        'message' => 'Paciente no encontrado'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

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
                ->innerJoin('dd.diagnostico_id', 'd')
                ->where('d.id IN (:diagnosticoIds)')
                ->setParameter('diagnosticoIds', $diagnosticoIds)
                ->orderBy('d.fecha', 'DESC')
                ->getQuery()
                ->getResult();

            $formattedResults = [];

            foreach ($detalleDiagnosticos as $detalleDiagnostico) {
                $diagnostico = $detalleDiagnostico->getDiagnosticoId();
                $formattedResults[] = [
                    'diagnostico_id' => $diagnostico->getId(),
                    'diagnostico' => [
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

    #[Route('/detalle_diagnostico/{id}', name: 'api_get_patient_medical_data', methods: ['GET'])]
    public function getGeneralPatientMedicalData(
        int $id,
        DiagnosticoRepository $diagnosticoRepository,
        DetalleDiagnosticoRepository $detalleDiagnosticoRepository
    ): JsonResponse {

        $error = fn(?int $diagnosticoId, string $message, int $status = 404) => $this->json([
            "diagnostico_id" => $diagnosticoId,
            "success" => false,
            "content" => ["message" => $message]
        ], $status);

        // Find the detalle_diagnostico by ID
        $diagnostico = $diagnosticoRepository->find($id);
        if (!$diagnostico) {
            return $error(null, "No se ha encontrado el diagnostico.");
        }

        // $ultimoDiagnostico = $diagnosticoRepository->findUltimoDiagnosticoPorPaciente($paciente);
        // if (!$ultimoDiagnostico) {
        //     return $error(null, "No se ha encontrado el diagnostico del paciente.");
        // }

        $detalle = $detalleDiagnosticoRepository->find($diagnostico->getId());
        if (!$detalle) {
            return $error($detalle, "No se han encontrado los detalles de este diagnostico.");
        }

        return $this->json([
            'success' => true,
            'message' => 'Medical data found',
            'content' => [
                'diagnostico' => [
                    'avd' => $detalle->getAvd(),
                    'o2' => $detalle->getO2(),
                    'o2_descripcion' => $detalle->getO2Descripcion(),
                    'panales' => $detalle->getPanales(),
                    'panales_descripcion' => $detalle->getPanalesDescripcion(),
                    'sv' => $detalle->getSv(),
                    'sr' => $detalle->getSr(),
                    'sng' => $detalle->getSng(),
                ],
                'id' => $diagnostico->getId(),
                'fecha' => $diagnostico->getFecha()->format('Y-m-d H:i:s'),
                'toma' => $diagnostico->getToma(),
                'paciente_id' => $diagnostico->getPacienteId()->getId(),
                'auxiliar_id' => $diagnostico->getAuxiliarId()->getId(),
            ],
        ]);
    }


    // POST
    #[Route('/detalle_diagnostico', name: 'api_create_detalle_diagnostico', methods: ['POST'])]
    public function createDetalleDiagnostico(
        Request $request,
        EntityManagerInterface $em,
        PacienteRepository $pacienteRepository,
        AuxiliarRepository $auxiliarRepository
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['paciente_id', 'auxiliar_id', 'avd', 'o2', 'o2_descripcion', 'panales', 'panales_descripcion', 'sv', 'sr', 'sng'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => "Falta el campo obligatorio: $field"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $paciente = $pacienteRepository->find($data['paciente_id']);
            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            $auxiliar = $auxiliarRepository->find($data['auxiliar_id']);
            if (!$auxiliar) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Auxiliar no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Crear diagnóstico
            $diagnostico = new Diagnostico();
            $diagnostico->setPacienteId($paciente);
            $diagnostico->setAuxiliarId($auxiliar);
            $diagnostico->setFecha(new \DateTime());

            $hospitalUtils = new HospitalUtils();

            $diagnostico->setToma(HospitalUtils::calcularToma(new \DateTime()));
            $diagnostico->setDiagnostico($data['diagnostico'] ?? '');
            $diagnostico->setMotivo($data['motivo'] ?? '');

            $em->persist($diagnostico);
            $em->flush();

            // Crear detalle
            $detalle = new DetalleDiagnostico();
            $detalle->setDiagnosticoId($diagnostico);
            $detalle->setAvd($data['avd']);
            $detalle->setO2((int) $data['o2']);
            $detalle->setO2Descripcion($data['o2_descripcion']);
            $detalle->setPanales((int) $data['panales']);
            $detalle->setPanalesDescripcion($data['panales_descripcion']);
            $detalle->setSv($data['sv']);
            $detalle->setSr($data['sr']);
            $detalle->setSng($data['sng']);

            $em->persist($detalle);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Diagnóstico y detalle creados con éxito',
                'diagnostico_id' => $diagnostico->getId(),
                'detalle_id' => $detalle->getId()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
