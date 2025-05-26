<?php

namespace App\Controller;

// OTHER
use App\Dto\CreateRegistroDto;
use App\Dto\RegistroInput;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\DetalleDiagnosticoType;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// DOCTRINE
use Doctrine\ORM\EntityManagerInterface;

// RESPONSES
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// ENTITIES
use App\Entity\Auxiliar;
use App\Entity\BalanceHidrico;
use App\Entity\ConstantesVitales;
use App\Entity\Dieta;
use App\Entity\Observacion;
use App\Entity\Registro;
use App\Entity\DetalleDiagnostico;
use App\Entity\DietaHasTipoDieta;
use App\Entity\Drenaje;
use App\Entity\Diagnostico;
use App\Entity\Higiene;
use App\Entity\Movilizacion;
use App\Entity\Paciente;
use App\Entity\Sueroterapia;
use App\Entity\TipoDieta;
use App\Entity\TipoHigiene;
use App\Entity\TipoTextura;

// REPOSITORIES
use App\Repository\AuxiliarRepository;
use App\Repository\TipoTexturaRepository;
use App\Repository\PacienteRepository;
use App\Repository\DetalleDiagnosticoRepository;
use App\Repository\DiagnosticoRepository;
use App\Repository\DietaHasTipoDietaRepository;
use App\Repository\HabitacionRepository;
use App\Repository\RegistroRepository;
use App\Repository\TipoDietaRepository;
use App\Repository\PacienteHasHabitacionesRepository;

final class HospitalDataController extends AbstractController
{

    #[Route('/diagnostico/paciente/{id}', name: 'api_diagnostico_by_paciente', methods: ['GET'])]
    public function getDiagnosticosByPaciente(int $id, DetalleDiagnosticoRepository $detalleDiagnosticoRepository, DiagnosticoRepository $diagnosticoRepository, PacienteRepository $pacienteRepository): JsonResponse
    {
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

    #[Route('/registro/paciente/historia/{id}', name: 'api_registro_historial_by_paciente', methods: ['GET'])]
    public function getRegistrosByPacienteHistorial(
        int $id,
        RegistroRepository $registroRepository,
        PacienteRepository $pacienteRepository
    ): JsonResponse {
        try {
            $paciente = $pacienteRepository->find($id);

            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'content' => ['message' => 'Paciente no encontrado']
                ], Response::HTTP_NOT_FOUND);
            }

            $registros = $registroRepository->findBy(['paciente_id' => $paciente]);

            if (empty($registros)) {
                return new JsonResponse([
                    'success' => false,
                    'content' => ['message' => 'No se encontraron registros para este paciente.']
                ], Response::HTTP_NOT_FOUND);
            }

            $formattedResults = [];

            foreach ($registros as $registro) {
                $constantes = $registro->getConstantesVitalesId();

                if ($constantes) {
                    $formattedResults[] = [
                        'timestamp' => $registro->getFecha()->format('Y-m-d\TH:i:s\Z'),
                        'constantes_vitales' => [
                            'ta_sistolica' => $constantes->getTaSistolica(),
                            'ta_diastolica' => $constantes->getTaDiastolica(),
                            'temperatura' => $constantes->getTemperatura(),
                            'pulso' => $constantes->getPulso(),
                            'frecuencia_respiratoria' => $constantes->getFrecuenciaRespiratoria(),
                            'spo2' => $constantes->getSaturacionOxigeno(),
                        ]
                    ];
                }
            }

            return $this->json([
                'success' => true,
                'content' => $formattedResults
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
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

            $registros = $registroRepository->findBy(
                ['paciente_id' => $paciente],
                ['fecha' => 'DESC']
            );

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
                $constantes_vitales = $registro->getConstantesVitalesId();

                $formattedResults[] = [
                    'registro_id' => $registro->getId(),
                    'registro' => [
                        'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                        'toma' => $registro->getToma(),
                        'nombre_auxiliar' => $auxiliar ? $auxiliar->getNombre() : null,
                        'numero_auxiliar' => $auxiliar ? $auxiliar->getNumTrabajador() : null,
                        'constantes_vitales' => [
                            'ta_sistolica' => $constantes_vitales ? $constantes_vitales->getTaSistolica() : null,
                            'ta_diastolica' => $constantes_vitales ? $constantes_vitales->getTaDiastolica() : null,
                            'frecuencia_respiratoria' => $constantes_vitales ? $constantes_vitales->getFrecuenciaRespiratoria() : null,
                            'pulso' => $constantes_vitales ? $constantes_vitales->getPulso() : null,
                            'temperatura' => $constantes_vitales ? $constantes_vitales->getTemperatura() : null,
                            'saturacion_oxigeno' => $constantes_vitales ? $constantes_vitales->getSaturacionOxigeno() : null
                        ],
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

    #[Route('/alertas', name: 'api_alertas_all', methods: ['GET'])]
    public function getAllAlertas(RegistroRepository $registroRepository, PacienteRepository $pacienteRepository): JsonResponse
    {
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


    #[Route('/registro/{id}', name: 'api_registro_by_id', methods: ['GET'])]
    public function getRegistroById(int $id, RegistroRepository $registroRepository, DietaHasTipoDietaRepository $dietaHasTipoDietaRepository, TipoDietaRepository $tipoDietaRepository): JsonResponse
    {
        try {

            $registro = $registroRepository->find($id);

            if (!$registro) {
                return $this->json([
                    'success' => false,
                    'message' => 'Registro not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $dieta = $registro->getDietaId();

            $tipoDietaDescriptions = [];

            $dietaHasTipoDietas = $dietaHasTipoDietaRepository->findBy(['dieta_id' => $dieta->getId()]);

            foreach ($dietaHasTipoDietas as $relation) {
                $tipoDietaId = $relation->getTipoDietaId();
                $tipoDieta = $tipoDietaRepository->find($tipoDietaId);

                if ($tipoDieta) {
                    $tipoDietaDescriptions[] = $tipoDieta->getDescripcion();
                }
            }

            $tipoDietaString = implode(', ', $tipoDietaDescriptions);

            $responseData = [
                'registro' => [
                    'constantes_vitales' => [
                        'ta_sistolica' => $registro->getConstantesVitalesId()->getTaSistolica(),
                        'ta_diastolica' => $registro->getConstantesVitalesId()->getTaDiastolica(),
                        'frecuencia_respiratoria' => $registro->getConstantesVitalesId()->getFrecuenciaRespiratoria(),
                        'pulso' => $registro->getConstantesVitalesId()->getPulso(),
                        'temperatura' => $registro->getConstantesVitalesId()->getTemperatura(),
                        'saturacion_oxigeno' => $registro->getConstantesVitalesId()->getSaturacionOxigeno()
                    ],

                    'dieta' => [
                        'autonomo' => $dieta->getAutonomo(),
                        'protesi' => $dieta->getProtesi(),
                        'tipo_dieta' => $tipoDietaString,
                        'tipo_textura' => $dieta->getTipoTexturaId()->getDescripcion(),
                    ],

                    'sueroterapia' => $registro->getSueroterapiaId()->getDosis(),

                    'balance_hidrico' => [
                        'diuresis' => $registro->getBalanceHidricoId()->getDiuresis(),
                        'deposicion' => $registro->getBalanceHidricoId()->getDeposicion(),
                    ],

                    'drenaje' => $registro->getDrenajeId()->getDescripcion(),

                    'movilizacion' => [
                        'sedestacion' => $registro->getMovilizacionId()->getSedestacion(),
                        'ayuda_deambulacion' => $registro->getMovilizacionId()->getAyudaDeambulacion(),
                        'ayuda_descripcion' => $registro->getMovilizacionId()->getAyudaDescripcion(),
                        'cambios_posturales' => $registro->getMovilizacionId()->getCambiosPosturales(),
                    ],

                    'higiene' => [
                        'tipo_higiene' => $registro->getHigieneId()->getTipo()->getDescripcion(),
                        'higiene_descripcion' => $registro->getHigieneId()->getDescripcion(),
                    ],

                    'observacion' => $registro->getObservacion()->getDescripcion(),

                    'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                    'auxiliar' => [
                        'nombre' => $registro->getAuxiliarId()->getNombre(),
                        'num_trabajador' => $registro->getAuxiliarId()->getNumTrabajador()
                    ]
                ],
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

    // Calcula la toma (Mañana, Tarde, Noche) según la fecha
    private function calcularToma(\DateTimeInterface $fecha): string
    {
        $hora = (int) $fecha->format('H');

        if ($hora >= 6 && $hora < 14) {
            return 'M'; // Mañana
        } elseif ($hora >= 14 && $hora < 22) {
            return 'T'; // Tarde
        } else {
            return 'N'; // Noche
        }
    }

    #[Route('/personal-data/{habitacion_codigo}', name: 'api_personal_data', methods: ['GET'])]
    public function getPersonalData(
        string $habitacion_codigo,
        EntityManagerInterface $entityManager,
        HabitacionRepository $habitacionRepository,
        PacienteRepository $pacienteRepository
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
            $diagnostico->setToma($this->calcularToma(new \DateTime()));
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


    private function calcularEdad(\DateTimeInterface $fechaNacimiento): int
    {
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNacimiento);
        return $edad->y;
    }

/* #[Route('/diets', name: 'api_all_diets', methods: ['GET'])]
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

        // Obtener todas las habitaciones ordenadas por código
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

            // Último paciente asignado a la habitación
            $asignacion = $entityManager->getRepository('App\Entity\PacienteHasHabitaciones')
                ->findOneBy(['habitacion_id' => $habitacion], ['timestamp' => 'DESC']);

            if (!$asignacion) {
                // Habitación vacía
                $results[] = [
                    'habitacion_codigo' => $habitacionCodigo,
                    'message' => 'empty room',
                    'isEmpty' => true,
                ];
                continue;
            }

            $paciente = $asignacion->getPacienteId();

            // Aplicar filtro si hay búsqueda
            if ($search !== '') {
                $nombre = mb_strtolower($paciente->getNombre());
                $apellidos = mb_strtolower($paciente->getApellidos());
                if (strpos($nombre, $search) === false && strpos($apellidos, $search) === false) {
                    continue;
                }
            }

            $edad = $this->calcularEdad($paciente->getFechaNacimiento());

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
                'alerta' => true
            ];
        }

        // Paginación
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
                'edad' => $this->calcularEdad($paciente->getFechaNacimiento()),
                'diagnostico' => $ultimoDiagnostico ? $ultimoDiagnostico->getDiagnostico() : null,
            ];

            $habitacionInfo['registro'] = $ultimoRegistro ? [
                'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                'observaciones' => $ultimoRegistro->getObservacion()->getDescripcion(),
                'alerta' => true,
            ] : null;
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
} */


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

        // Obtener todas las habitaciones ordenadas por código
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

            // Último paciente asignado a la habitación
            $asignacion = $entityManager->getRepository('App\Entity\PacienteHasHabitaciones')
                ->findOneBy(['habitacion_id' => $habitacion], ['timestamp' => 'DESC']);

            if (!$asignacion) {
                // Habitación vacía
                $results[] = [
                    'habitacion_codigo' => $habitacionCodigo,
                    'message' => 'empty room',
                    'isEmpty' => true,
                ];
                continue;
            }

            $paciente = $asignacion->getPacienteId();

            // Aplicar filtro si hay búsqueda
            if ($search !== '') {
                $nombre = mb_strtolower($paciente->getNombre());
                $apellidos = mb_strtolower($paciente->getApellidos());
                if (strpos($nombre, $search) === false && strpos($apellidos, $search) === false) {
                    continue;
                }
            }

            $edad = $this->calcularEdad($paciente->getFechaNacimiento());

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
                'alerta' => true
            ];
        }

        // Paginación
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
                'edad' => $this->calcularEdad($paciente->getFechaNacimiento()),
                'diagnostico' => $ultimoDiagnostico ? $ultimoDiagnostico->getDiagnostico() : null,
            ];

            $habitacionInfo['registro'] = $ultimoRegistro ? [
                'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                'observaciones' => $ultimoRegistro->getObservacion()->getDescripcion(),
                'alerta' => true,
            ] : null;
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




    #[Route('/detalle_diagnostico/{id}', name: 'api_get_patient_medical_data', methods: ['GET'])]
    public function getGeneralPatientMedicalData(
        int $id,
        PacienteRepository $pacienteRepository,
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
}
