<?php

namespace App\Controller;

// OTHER
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// DOCTRINE
use Doctrine\ORM\EntityManagerInterface;

// RESPONSES
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// REPOSITORIES
use App\Repository\PacienteRepository;
use App\Repository\DiagnosticoRepository;
use App\Repository\DietaHasTipoDietaRepository;
use App\Repository\HabitacionRepository;
use App\Repository\RegistroRepository;
use App\Repository\TipoDietaRepository;
use App\Repository\PacienteHasHabitacionesRepository;

final class HospitalDataController extends AbstractController
{
   #[Route('/general', name: 'app_habitacion_index', methods: ['GET'])]
public function getAllRooms(
    Request $request,
    HabitacionRepository $habitacionRepository,
    PacienteHasHabitacionesRepository $pacienteHasHabitacionesRepository,
    DiagnosticoRepository $diagnosticoRepository,
    RegistroRepository $registroRepository
): JsonResponse {
    $page = max(1, (int) $request->query->get('page', 1));
    $limit = max(1, (int) $request->query->get('limit', 16));
    $offset = ($page - 1) * $limit;
    $search = $request->query->get('search', '');

    if ($search) {
        $habitaciones = $habitacionRepository->createQueryBuilder('h')
            ->where('h.codigo LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalHabitaciones = $habitacionRepository->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.codigo LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getSingleScalarResult();
    } else {
        $habitaciones = $habitacionRepository->findBy([], null, $limit, $offset);
        $totalHabitaciones = $habitacionRepository->count([]);
    }

    $rangosNormales = [
    'ta_sistolica' => ['min' => 90, 'max' => 140], // Changed min from 80 to 90
    'ta_diastolica' => ['min' => 50, 'max' => 90], // Unchanged
    'frecuencia_respiratoria' => ['min' => 12, 'max' => 20], // Changed from 10–24 to 12–20
    'pulso' => ['min' => 50, 'max' => 100], // Changed max from 110 to 100
    'temperatura' => ['min' => 34.9, 'max' => 38.5], // Changed from 35.5–38.0 to 34.9–38.5
    'saturacion_oxigeno' => ['min' => 94, 'max' => 100], // Changed min from 90 to 94
];

    $data = [];

    foreach ($habitaciones as $habitacion) {
        $habitacionInfo = [
            'habitacion_codigo' => $habitacion->getCodigo(),
        ];

        $paciente = $pacienteHasHabitacionesRepository->findUltimoPacientePorHabitacion($habitacion);

        if (!$paciente) {
            $habitacionInfo['isEmpty'] = true;
        } else {
            $habitacionInfo['isEmpty'] = false;

            $ultimoDiagnostico = $diagnosticoRepository->findUltimoDiagnosticoPorPaciente($paciente);
            $ultimoRegistro = $registroRepository->findByUltimoPorPaciente($paciente);

            $habitacionInfo['paciente'] = [
                'id' => $paciente->getId(),
                'nombre' => $paciente->getNombre(),
                'apellidos' => $paciente->getApellidos(),
                'edad' => HospitalUtils::calcularEdad($paciente->getFechaNacimiento()),
                'diagnostico' => $ultimoDiagnostico ? $ultimoDiagnostico->getDiagnostico() : null,
            ];

            $alerta = false;
            $vitalSigns = null;
            if ($ultimoRegistro) {
                $constantesVitales = $ultimoRegistro->getConstantesVitalesId();
                if ($constantesVitales) {
                    $vitalSigns = [
                        'ta_sistolica' => $constantesVitales->getTaSistolica(),
                        'ta_diastolica' => $constantesVitales->getTaDiastolica(),
                        'frecuencia_respiratoria' => $constantesVitales->getFrecuenciaRespiratoria(),
                        'pulso' => $constantesVitales->getPulso(),
                        'temperatura' => $constantesVitales->getTemperatura(),
                        'saturacion_oxigeno' => $constantesVitales->getSaturacionOxigeno(),
                    ];

                    if (
                        ($constantesVitales->getTaSistolica() !== null &&
                            ($constantesVitales->getTaSistolica() < $rangosNormales['ta_sistolica']['min'] ||
                             $constantesVitales->getTaSistolica() > $rangosNormales['ta_sistolica']['max'])) ||
                        ($constantesVitales->getTaDiastolica() !== null &&
                            ($constantesVitales->getTaDiastolica() < $rangosNormales['ta_diastolica']['min'] ||
                             $constantesVitales->getTaDiastolica() > $rangosNormales['ta_diastolica']['max'])) ||
                        ($constantesVitales->getFrecuenciaRespiratoria() !== null &&
                            ($constantesVitales->getFrecuenciaRespiratoria() < $rangosNormales['frecuencia_respiratoria']['min'] ||
                             $constantesVitales->getFrecuenciaRespiratoria() > $rangosNormales['frecuencia_respiratoria']['max'])) ||
                        ($constantesVitales->getPulso() !== null &&
                            ($constantesVitales->getPulso() < $rangosNormales['pulso']['min'] ||
                             $constantesVitales->getPulso() > $rangosNormales['pulso']['max'])) ||
                        ($constantesVitales->getTemperatura() !== null &&
                            ($constantesVitales->getTemperatura() < $rangosNormales['temperatura']['min'] ||
                             $constantesVitales->getTemperatura() > $rangosNormales['temperatura']['max'])) ||
                        ($constantesVitales->getSaturacionOxigeno() !== null &&
                            ($constantesVitales->getSaturacionOxigeno() < $rangosNormales['saturacion_oxigeno']['min'] ||
                             $constantesVitales->getSaturacionOxigeno() > $rangosNormales['saturacion_oxigeno']['max']))
                    ) {
                        $alerta = true;
                    }
                }

                $habitacionInfo['registro'] = [
                    'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                    'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                    'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                    'observaciones' => $ultimoRegistro->getObservacion() ? $ultimoRegistro->getObservacion()->getDescripcion() : null,
                    'alerta' => $alerta,
                    'constantes_vitales' => $vitalSigns,
                ];
            } else {
                $habitacionInfo['registro'] = null;
            }
        }

        $data[] = $habitacionInfo;
    }

    return $this->json([
        'success' => true,
        'message' => 'List rooms correct',
        'habitacion' => $data,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total_items' => (int) $totalHabitaciones,
            'total_pages' => ceil($totalHabitaciones / $limit),
        ],
    ]);
}

   #[Route('/diets', name: 'api_all_diets', methods: ['GET'])]
    public function getAllDiets(
        Request $request,
        EntityManagerInterface $entityManager,
        HabitacionRepository $habitacionRepository,
        RegistroRepository $registroRepository,
        DietaHasTipoDietaRepository $dietaHasTipoDietaRepository,
        TipoDietaRepository $tipoDietaRepository
    ): JsonResponse {
        try {
            $page = max(1, (int)$request->query->get('page', 1));
            $itemsPerPage = max(1, (int)$request->query->get('itemsPerPage', 16));
            $search = mb_strtolower(trim($request->query->get('search', '')));

            // Define less strict vital signs ranges
           $rangosNormales = [
    'ta_sistolica' => ['min' => 90, 'max' => 140], // Changed min from 80 to 90
    'ta_diastolica' => ['min' => 50, 'max' => 90], // Unchanged
    'frecuencia_respiratoria' => ['min' => 12, 'max' => 20], // Changed from 10–24 to 12–20
    'pulso' => ['min' => 50, 'max' => 100], // Changed max from 110 to 100
    'temperatura' => ['min' => 34.9, 'max' => 38.5], // Changed from 35.5–38.0 to 34.9–38.5
    'saturacion_oxigeno' => ['min' => 94, 'max' => 100], // Changed min from 90 to 94
];

            // Get all rooms ordered by code
            $habitaciones = $habitacionRepository->createQueryBuilder('h')
                ->orderBy('h.codigo', 'ASC')
                ->getQuery()
                ->getResult();

            if (empty($habitaciones)) {
                return $this->json([
                    'success' => false,
                    'habitacion' => [
                        'message' => 'No se encontraron habitaciones'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $results = [];

            foreach ($habitaciones as $habitacion) {
                $habitacionCodigo = $habitacion->getCodigo();

                // Last patient assigned to the room
                $asignacion = $entityManager->getRepository('App\Entity\PacienteHasHabitaciones')
                    ->findOneBy(['habitacion_id' => $habitacion], ['timestamp' => 'DESC']);

                if (!$asignacion) {
                    $results[] = [
                        'habitacion_codigo' => $habitacionCodigo,
                        'message' => 'empty room',
                        'isEmpty' => true,
                    ];
                    continue;
                }

                $paciente = $asignacion->getPacienteId();

                // Apply search filter
                if ($search !== '') {
                    $nombre = mb_strtolower($paciente->getNombre());
                    $apellidos = mb_strtolower($paciente->getApellidos());
                    if (strpos($nombre, $search) === false && strpos($apellidos, $search) === false) {
                        continue;
                    }
                }

                $edad = HospitalUtils::calcularEdad($paciente->getFechaNacimiento());

                $registro = $registroRepository->findOneBy(['paciente_id' => $paciente], ['fecha' => 'DESC']);
                if (!$registro || !$registro->getDietaId()) {
                    continue;
                }

                $dieta = $registro->getDietaId();
                $tipoDietaDescriptions = [];

                $dietaHasTipoDietas = $dietaHasTipoDietaRepository->findBy(['dieta_id' => $dieta->getId()]);
                foreach ($dietaHasTipoDietas as $relation) {
                    $tipoDieta = $tipoDietaRepository->find($relation->getTipoDietaId());
                    if ($tipoDieta) {
                        $tipoDietaDescriptions[] = $tipoDieta->getDescripcion();
                    }
                }

                // Calculate alert based on vital signs
                $alerta = false;
                $vitalSigns = null;
                if ($registro) {
                    $constantesVitales = $registro->getConstantesVitalesId();
                    if ($constantesVitales) {
                        $vitalSigns = [
                            'ta_sistolica' => $constantesVitales->getTaSistolica(),
                            'ta_diastolica' => $constantesVitales->getTaDiastolica(),
                            'frecuencia_respiratoria' => $constantesVitales->getFrecuenciaRespiratoria(),
                            'pulso' => $constantesVitales->getPulso(),
                            'temperatura' => $constantesVitales->getTemperatura(),
                            'saturacion_oxigeno' => $constantesVitales->getSaturacionOxigeno(),
                        ];

                        if (
                            ($constantesVitales->getTaSistolica() !== null &&
                                ($constantesVitales->getTaSistolica() < $rangosNormales['ta_sistolica']['min'] ||
                                 $constantesVitales->getTaSistolica() > $rangosNormales['ta_sistolica']['max'])) ||
                            ($constantesVitales->getTaDiastolica() !== null &&
                                ($constantesVitales->getTaDiastolica() < $rangosNormales['ta_diastolica']['min'] ||
                                 $constantesVitales->getTaDiastolica() > $rangosNormales['ta_diastolica']['max'])) ||
                            ($constantesVitales->getFrecuenciaRespiratoria() !== null &&
                                ($constantesVitales->getFrecuenciaRespiratoria() < $rangosNormales['frecuencia_respiratoria']['min'] ||
                                 $constantesVitales->getFrecuenciaRespiratoria() > $rangosNormales['frecuencia_respiratoria']['max'])) ||
                            ($constantesVitales->getPulso() !== null &&
                                ($constantesVitales->getPulso() < $rangosNormales['pulso']['min'] ||
                                 $constantesVitales->getPulso() > $rangosNormales['pulso']['max'])) ||
                            ($constantesVitales->getTemperatura() !== null &&
                                ($constantesVitales->getTemperatura() < $rangosNormales['temperatura']['min'] ||
                                 $constantesVitales->getTemperatura() > $rangosNormales['temperatura']['max'])) ||
                            ($constantesVitales->getSaturacionOxigeno() !== null &&
                                ($constantesVitales->getSaturacionOxigeno() < $rangosNormales['saturacion_oxigeno']['min'] ||
                                 $constantesVitales->getSaturacionOxigeno() > $rangosNormales['saturacion_oxigeno']['max']))
                        ) {
                            $alerta = true;
                        }
                    }
                }

                $results[] = [
                    'habitacion_codigo' => $habitacionCodigo,
                    'paciente' => [
                        'id' => $paciente->getId(),
                        'nombre' => $paciente->getNombre(),
                        'apellidos' => $paciente->getApellidos(),
                        'edad' => $edad
                    ],
                    'detalle' => [
                        'tipo_dieta' => implode(', ', $tipoDietaDescriptions),
                        'textura' => $dieta->getTipoTexturaId()?->getDescripcion(),
                        'protesis' => $dieta->getProtesi() ? 'Sí' : 'No',
                        'asistencia' => $dieta->getAutonomo() ? 'Independiente' : 'Dependiente',
                        'observaciones' => $registro->getObservacion()?->getDescripcion() ?? '',
                    ],
                    'isEmpty' => false,
                    'alerta' => $alerta,
                    'constantes_vitales' => $vitalSigns,
                ];
            }

            // Pagination
            $totalItems = count($results);
            $totalPages = (int) ceil($totalItems / $itemsPerPage);
            $offset = ($page - 1) * $itemsPerPage;
            $pagedResults = array_slice($results, $offset, $itemsPerPage);

            return $this->json([
                'success' => true,
                'habitacion' => $pagedResults,
                'pagination' => [
                    'total_items' => $totalItems,
                    'total_pages' => $totalPages,
                    'current_page' => $page,
                    'items_per_page' => $itemsPerPage,
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => [
                    'message' => 'Error interno: ' . $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/personal-data/{habitacion_codigo}', name: 'api_personal_data', methods: ['GET'])]
    public function getPersonalData(
        string $habitacion_codigo,
        EntityManagerInterface $entityManager,
        HabitacionRepository $habitacionRepository,
    ): JsonResponse {
        try {
            // Buscar la habitación por su código
            $habitacion = $habitacionRepository->findOneBy(['codigo' => $habitacion_codigo]);

            if (!$habitacion) {
                return $this->json([
                    'success' => false,
                    'content' => [
                        'message' => 'Habitación no encontrada'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            // Buscar la asignación más reciente de paciente a la habitación
            $asignacion = $entityManager->getRepository('App\Entity\PacienteHasHabitaciones')
                ->findOneBy(['habitacion_id' => $habitacion], ['timestamp' => 'DESC']);

            if (!$asignacion) {
                return $this->json([
                    'success' => true,
                    'content' => [
                        [
                            'habitacion_codigo' => $habitacion_codigo,
                            'message' => 'empty room'
                        ]
                    ]
                ], Response::HTTP_OK);
            }

            // Obtener el paciente
            $paciente = $asignacion->getPacienteId();

            // Formatear la respuesta
            $formattedResult = [
                'habitacion_codigo' => $habitacion_codigo,
                'paciente' => [
                    'nombre' => $paciente->getNombre(),
                    'apellidos' => $paciente->getApellidos(),
                    'fecha_nacimiento' => $paciente->getFechaNacimiento() ? $paciente->getFechaNacimiento()->format('Y-m-d') : null,
                    'direccion_completa' => $paciente->getDireccionCompleta(),
                    'lengua_materna' => $paciente->getLenguaMaterna(),
                    'alergias' => $paciente->getAlergias(),
                    'antecedentes' => $paciente->getAntecedentes(),
                    'nombre_cuidador' => $paciente->getNombreCuidador(),
                    'telefono_cuidador' => $paciente->getTelefonoCuidador()
                ]
            ];

            return $this->json([
                'success' => true,
                'content' => [$formattedResult]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => [
                    'message' => 'Error interno: ' . $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/alertas', name: 'api_alertas_all', methods: ['GET'])]
    public function getAllAlertas(
        RegistroRepository $registroRepository,
        PacienteRepository $pacienteRepository
    ): JsonResponse {
        try {
            $pacientes = $pacienteRepository->findAll();

            if (empty($pacientes)) {
                return new JsonResponse([
                    'success' => false,
                    'content' => [
                        'message' => 'No se encontraron pacientes'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $rangosNormales = [
                'ta_sistolica' => ['min' => 90, 'max' => 120],
                'ta_diastolica' => ['min' => 60, 'max' => 80],
                'frecuencia_respiratoria' => ['min' => 12, 'max' => 20],
                'pulso' => ['min' => 60, 'max' => 100],
                'temperatura' => ['min' => 36.1, 'max' => 37.2],
                'saturacion_oxigeno' => ['min' => 95, 'max' => 100],
            ];

            $alertas = [];
            foreach ($pacientes as $index => $paciente) {
                $registros = $registroRepository->findBy(
                    ['paciente_id' => $paciente],
                    ['fecha' => 'DESC'],
                    1,
                    0
                );

                if (empty($registros)) {
                    continue;
                }

                $registro = $registros[0];
                $constantesVitales = $registro->getConstantesVitalesId();
                if (!$constantesVitales) {
                    continue;
                }

                // Generar un número de habitación como "H001", "H002", etc.
                $roomNumber = sprintf('H%03d', $index + 1); // H001, H002, ...

                $alerta = [
                    'registro_id' => $registro->getId(),
                    'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                    'paciente_id' => $paciente->getId(),
                    'paciente_nombre' => $paciente->getNombre(),
                    'room' => $roomNumber, // Añadimos el campo room
                    'alertas' => []
                ];

                if (
                    $constantesVitales->getTaSistolica() !== null &&
                    ($constantesVitales->getTaSistolica() < $rangosNormales['ta_sistolica']['min'] ||
                        $constantesVitales->getTaSistolica() > $rangosNormales['ta_sistolica']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'ta_sistolica',
                        'valor' => $constantesVitales->getTaSistolica(),
                        'mensaje' => 'Presión arterial sistólica fuera del rango normal (' .
                            $rangosNormales['ta_sistolica']['min'] . '-' .
                            $rangosNormales['ta_sistolica']['max'] . ' mmHg)'
                    ];
                }

                if (
                    $constantesVitales->getTaDiastolica() !== null &&
                    ($constantesVitales->getTaDiastolica() < $rangosNormales['ta_diastolica']['min'] ||
                        $constantesVitales->getTaDiastolica() > $rangosNormales['ta_diastolica']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'ta_diastolica',
                        'valor' => $constantesVitales->getTaDiastolica(),
                        'mensaje' => 'Presión arterial diastólica fuera del rango normal (' .
                            $rangosNormales['ta_diastolica']['min'] . '-' .
                            $rangosNormales['ta_diastolica']['max'] . ' mmHg)'
                    ];
                }

                if (
                    $constantesVitales->getFrecuenciaRespiratoria() !== null &&
                    ($constantesVitales->getFrecuenciaRespiratoria() < $rangosNormales['frecuencia_respiratoria']['min'] ||
                        $constantesVitales->getFrecuenciaRespiratoria() > $rangosNormales['frecuencia_respiratoria']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'frecuencia_respiratoria',
                        'valor' => $constantesVitales->getFrecuenciaRespiratoria(),
                        'mensaje' => 'Frecuencia respiratoria fuera del rango normal (' .
                            $rangosNormales['frecuencia_respiratoria']['min'] . '-' .
                            $rangosNormales['frecuencia_respiratoria']['max'] . ' respiraciones/min)'
                    ];
                }

                if (
                    $constantesVitales->getPulso() !== null &&
                    ($constantesVitales->getPulso() < $rangosNormales['pulso']['min'] ||
                        $constantesVitales->getPulso() > $rangosNormales['pulso']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'pulso',
                        'valor' => $constantesVitales->getPulso(),
                        'mensaje' => 'Pulso fuera del rango normal (' .
                            $rangosNormales['pulso']['min'] . '-' .
                            $rangosNormales['pulso']['max'] . ' latidos/min)'
                    ];
                }

                if (
                    $constantesVitales->getTemperatura() !== null &&
                    ($constantesVitales->getTemperatura() < $rangosNormales['temperatura']['min'] ||
                        $constantesVitales->getTemperatura() > $rangosNormales['temperatura']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'temperatura',
                        'valor' => $constantesVitales->getTemperatura(),
                        'mensaje' => 'Temperatura fuera del rango normal (' .
                            $rangosNormales['temperatura']['min'] . '-' .
                            $rangosNormales['temperatura']['max'] . ' °C)'
                    ];
                }

                if (
                    $constantesVitales->getSaturacionOxigeno() !== null &&
                    ($constantesVitales->getSaturacionOxigeno() < $rangosNormales['saturacion_oxigeno']['min'] ||
                        $constantesVitales->getSaturacionOxigeno() > $rangosNormales['saturacion_oxigeno']['max'])
                ) {
                    $alerta['alertas'][] = [
                        'parametro' => 'saturacion_oxigeno',
                        'valor' => $constantesVitales->getSaturacionOxigeno(),
                        'mensaje' => 'Saturación de oxígeno fuera del rango normal (' .
                            $rangosNormales['saturacion_oxigeno']['min'] . '-' .
                            $rangosNormales['saturacion_oxigeno']['max'] . ' %)'
                    ];
                }

                if (!empty($alerta['alertas'])) {
                    $alertas[] = $alerta;
                }
            }

            if (empty($alertas)) {
                return new JsonResponse([
                    'success' => true,
                    'content' => [
                        'message' => 'No se encontraron alertas para ningún paciente',
                        'alertas' => []
                    ]
                ], Response::HTTP_OK);
            }

            return new JsonResponse([
                'success' => true,
                'content' => [
                    'message' => 'Alertas encontradas para todos los pacientes',
                    'alertas' => $alertas
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'content' => [
                    'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}