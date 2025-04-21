<?php

namespace App\Controller;

use App\Dto\CreateRegistroDto;
use App\Entity\BalanceHidrico;
use App\Entity\ConstantesVitales;
use App\Entity\Dieta;
use App\Entity\Observacion;
use App\Entity\Registro;
use App\Repository\TipoTexturaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\DetalleDiagnostico;
use App\Entity\DietaHasTipoDieta;
use App\Entity\Drenaje;
use App\Entity\Higiene;
use App\Entity\Movilizacion;
use App\Entity\Sueroterapia;
use App\Entity\TipoDieta;
use App\Form\DetalleDiagnosticoType;
use App\Repository\DetalleDiagnosticoRepository;
use App\Repository\DiagnosticoRepository;
use App\Repository\DietaHasTipoDietaRepository;
use App\Repository\HabitacionRepository;
use App\Repository\PacienteRepository;
use App\Repository\RegistroRepository;
use App\Repository\TipoDietaRepository;
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
    public function getDiagnosticosByPaciente(int $id, DetalleDiagnosticoRepository $detalleDiagnosticoRepository, DiagnosticoRepository $diagnosticoRepository, PacienteRepository $pacienteRepository): JsonResponse
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

    #[Route('/registro/{id}', name: 'api_registro_by_id', methods: ['GET'])]
    public function getRegistroById(int $id, RegistroRepository $registroRepository, DietaHasTipoDietaRepository $dietaHasTipoDietaRepository, TipoDietaRepository $tipoDietaRepository): JsonResponse
    {
        try {

            // Find the registro by ID
            $registro = $registroRepository->find($id);

            // Return 404 if not found
            if (!$registro) {
                return $this->json([
                    'success' => false,
                    'message' => 'Registro not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Get the entities associated with the registro
            $dieta = $registro->getDietaId();

            // Handle the tipo_dieta many-to-many relationship
            $tipoDietaDescriptions = [];

            // Get all DietaHasTipoDieta records for this dieta
            $dietaHasTipoDietas = $dietaHasTipoDietaRepository->findBy(['dieta_id' => $dieta->getId()]);

            // Extract the description from each TipoDieta
            foreach ($dietaHasTipoDietas as $relation) {
                $tipoDietaId = $relation->getTipoDietaId();
                $tipoDieta = $tipoDietaRepository->find($tipoDietaId);

                if ($tipoDieta) {
                    $tipoDietaDescriptions[] = $tipoDieta->getDescripcion();
                }
            }

            // Combine all descriptions into a single string
            $tipoDietaString = implode(', ', $tipoDietaDescriptions);

            // Construct the response data according to the required format
            $responseData = [
                'registro' => [
                    // Constantes vitales section
                    'constantes_vitales' => [
                        'ta_sistolica' => $registro->getConstantesVitalesId()->getTaSistolica(),
                        'ta_diastolica' => $registro->getConstantesVitalesId()->getTaDiastolica(),
                        'frecuencia_respiratoria' => $registro->getConstantesVitalesId()->getFrecuenciaRespiratoria(),
                        'temperatura' => $registro->getConstantesVitalesId()->getTemperatura(),
                        'saturacion_oxigeno' => $registro->getConstantesVitalesId()->getSaturacionOxigeno()
                    ],

                    // Dieta section with the tipo_dieta relationship
                    'dieta' => [
                        'autonomo' => $dieta->getAutonomo(),
                        'protesi' => $dieta->getProtesi(),
                        'tipo_dieta' => $tipoDietaString,
                        'tipo_textura' => $dieta->getTipoTexturaId()->getDescripcion(),
                    ],

                    // Sueroterapia section
                    'sueroterapia' => $registro->getSueroterapiaId()->getDosis(),

                    // Balance hidrico section
                    'balance_hidrico' => [
                        'diuresis' => $registro->getBalanceHidricoId()->getDiuresis(),
                        'deposicion' => $registro->getBalanceHidricoId()->getDeposicion(),
                    ],

                    // Drenaje section
                    'drenaje' => $registro->getDrenajeId()->getDescripcion(),

                    // Movilizacion section
                    'movilizacion' => [
                        'sedestacion' => $registro->getMovilizacionId()->getSedestacion(),
                        'ayuda_deambulacion' => $registro->getMovilizacionId()->getAyudaDeambulacion(),
                        'ayuda_decripcion' => $registro->getMovilizacionId()->getAyudaDescripcion(),
                        'cambios_posturales' => $registro->getMovilizacionId()->getCambiosPosturales(),
                    ],

                    // Higiene section
                    'higiene' => [
                        'tipo_higiene' => $registro->getHigieneId()->getTipo()->getDescripcion(),
                        'higiene_descripcion' => $registro->getHigieneId()->getDescripcion(),
                    ],

                    // Observacion section
                    'observacion' => $registro->getObservacion()->getDescripcion()
                ]
            ];

            return new JsonResponse([
                'success' => true,
                'content' => $responseData
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'Error interno: ' . $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/registro/paciente/{id}', name: 'api_registros_by_paciente', methods: ['GET'])]
    public function getRegistrosByPaciente(int $id, RegistroRepository $registroRepository, PacienteRepository $pacienteRepository): JsonResponse
    {
        try {
            $paciente = $pacienteRepository->find($id);

            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'content' => [
                        'message' => 'Paciente no encontrado'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $registros = $registroRepository->findBy(['paciente_id' => $paciente]);

            if (empty($registros)) {
                return new JsonResponse([
                    'success' => false,
                    'content' => [
                        'message' => 'No se encontraron registros para este paciente'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $formattedResults = [];
            foreach ($registros as $registro) {
                $auxiliar = $registro->getAuxiliarId();
                $observacion = $registro->getObservacion();

                $formattedResults[] = [
                    'registro_id' => $registro->getId(),
                    'registro' => [
                        'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                        'toma' => $registro->getToma(),
                        'nombre_auxiliar' => $auxiliar ? $auxiliar->getNombre() : null,
                        'numero_auxiliar' => $auxiliar ? $auxiliar->getNumTrabajador() : null,
                        'observacion' => $observacion ? $observacion->getDescripcion() : null
                    ]
                ];
            }

            return new JsonResponse([
                'success' => true,
                'content' => $formattedResults
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'Error interno: ' . $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}