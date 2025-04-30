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
use App\Entity\Higiene;
use App\Entity\Movilizacion;
use App\Entity\Paciente;
use App\Entity\Sueroterapia;
use App\Entity\TipoDieta;
use App\Entity\TipoHigiene;
use App\Entity\TipoTextura;

// REPOSITORIES
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

    #[Route('/registro/paciente/{id}', name: 'create_registros_by_paciente', methods: ['POST'])]
    public function createRegistroByPaciente(
        Request $request,
        SerializerSerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        PacienteRepository $pacienteRepository,
        int $id
    ): JsonResponse {
        try {

            // Get paciente
            $paciente = $pacienteRepository->find($id);

            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'content' => [
                        'message' => 'Paciente no encontrado'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $data = $request->getContent();
            $registroInput = $serializer->deserialize($data, RegistroInput::class, 'json');
            $errors = $validator->validate($registroInput);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $registro = new Registro();
            $fecha = new \DateTime('now', new \DateTimeZone('UTC'));
            $fecha->setTimezone(new \DateTimeZone('Europe/Madrid'));
            $registro->setFecha($fecha);
            $registro->setToma($this->calcularToma($fecha));

            $registro->setPacienteId($paciente);
            // $registro->setAuxiliarId($em->getReference(Auxiliar::class, $registroInput->auxiliarId));

            // AUXILIAR AUTH 
            // $auxiliar = $em->getRepository(Auxiliar::class)->findOneBy([
            //     'numTrabajador' => $this->getUser()->getUserIdentifier()
            // ]);
            // $registro->setAuxiliar($auxiliar);

            // Observacion
            if ($registroInput->observacion) {
                $obs = new Observacion();
                $obs->setDescripcion($registroInput->observacion->descripcion);
                $em->persist($obs);
                $registro->setObservacion($obs);
            }

            // Dieta
            if ($registroInput->dieta) {
                $dieta = new Dieta();
                $dieta->setAutonomo($registroInput->dieta->autonomo);
                $dieta->setProtesi($registroInput->dieta->protesi);
                $dieta->setTipoTexturaId($em->getReference(TipoTextura::class, $registroInput->dieta->tipoTexturaId));
                $em->persist($dieta);

                foreach ($registroInput->dieta->tipoDietaId as $tipo) {
                    $tipoDieta = new DietaHasTipoDieta();

                    $tipoDieta->setDietaId($dieta);
                    $tipoDieta->setTipoDietaId($em->getReference(TipoDieta::class, $tipo));
                    $em->persist($tipoDieta);
                }

                $registro->setDietaId($dieta);
            }

            // Drenaje
            if ($registroInput->drenaje) {
                $dren = new Drenaje();
                $dren->setDescripcion($registroInput->drenaje->descripcion);
                $em->persist($dren);
                $registro->setDrenajeId($dren);
            }

            // Higiene
            if ($registroInput->higiene) {
                $hig = new Higiene();
                $hig->setDescripcion($registroInput->higiene->descripcion);
                $hig->setTipo($em->getReference(TipoHigiene::class, $registroInput->higiene->tipoId));
                $em->persist($hig);
                $registro->setHigieneId($hig);
            }

            // Constantes Vitales
            if ($registroInput->constantesVitales) {
                $cv = new ConstantesVitales();
                $cv->setTaSistolica($registroInput->constantesVitales->taSistolica);
                $cv->setTaDiastolica($registroInput->constantesVitales->taDiastolica);
                $cv->setFrecuenciaRespiratoria($registroInput->constantesVitales->frecuenciaRespiratoria);
                $cv->setPulso($registroInput->constantesVitales->pulso);
                $cv->setTemperatura($registroInput->constantesVitales->temperatura);
                $cv->setSaturacionOxigeno($registroInput->constantesVitales->saturacionOxigeno);
                $em->persist($cv);
                $registro->setConstantesVitalesId($cv);
            }

            // Movilización
            if ($registroInput->movilizacion) {
                $mov = new Movilizacion();
                $mov->setSedestacion($registroInput->movilizacion->sedestacion);
                $mov->setAyudaDeambulacion($registroInput->movilizacion->ayudaDeambulacion);
                $mov->setAyudaDescripcion($registroInput->movilizacion->ayudaDescripcion);
                $mov->setCambiosPosturales($registroInput->movilizacion->cambiosPosturales);
                $em->persist($mov);
                $registro->setMovilizacionId($mov);
            }

            // Sueroterapia
            if ($registroInput->sueroterapia) {
                $stp = new Sueroterapia();
                $stp->setDosis($registroInput->sueroterapia->dosis);
                $em->persist($stp);
                $registro->setSueroterapiaId($stp);
            }

            // Balance Hidrico
            if ($registroInput->balanceHidrico) {
                $bh = new BalanceHidrico();
                $bh->setDiuresis($registroInput->balanceHidrico->diuresis);
                $bh->setDeposicion($registroInput->balanceHidrico->deposicion);
                $em->persist($bh);
                $registro->setBalanceHidricoId($bh);
            }

            // Save all
            $em->persist($registro);
            $em->flush();

            return $this->json([
                'success' => true,
                'content' => [
                    'message' => 'Registro created successfully',
                    'registro_id' => $registro->getId()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
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
        HabitacionRepository $habitacionRepository,
        PacienteRepository $pacienteRepository,
        DiagnosticoRepository $diagnosticoRepository,
        RegistroRepository $registroRepository,
        PacienteHasHabitacionesRepository $pacienteHasHabitacionesRepository
    ): JsonResponse {
        try {
            $habitaciones = $habitacionRepository->findAll();
            $data = [];

            if (!empty($habitaciones)) {
                foreach ($habitaciones as $habitacion) {
                    $habitacionInfo = [
                        'habitacion_codigo' => $habitacion->getCodigo(),
                    ];

                    // Obtener el último paciente relacionado con la habitación
                    $paciente = $pacienteHasHabitacionesRepository->findUltimoPacientePorHabitacion($habitacion);

                    if (!$paciente) {
                        $habitacionInfo['isEmpty'] = true;
                    } else {
                        $habitacionInfo['isEmpty'] = false;

                        // Obtener el último diagnóstico del paciente
                        $ultimoDiagnostico = $diagnosticoRepository->findUltimoDiagnosticoPorPaciente($paciente);

                        // Obtener el último registro del paciente
                        $ultimoRegistro = $registroRepository->findByUltimoPorPaciente($paciente);

                        // Detalles del paciente
                        $habitacionInfo['paciente'] = [
                            'nombre' => $paciente->getNombre(),
                            'apellidos' => $paciente->getApellidos(),
                            'edad' => $this->calcularEdad($paciente->getFechaNacimiento()),
                            'diagnostico' => $ultimoDiagnostico ? $ultimoDiagnostico->getDiagnostico() : null,
                        ];

                        // Detalles del último registro
                        $habitacionInfo['registro'] = $ultimoRegistro ? [
                            'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                            'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                            'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                            'observaciones' => $ultimoRegistro->getObservacion()->getDescripcion(),
                            'alerta' => true,
                        ] : null;
                    }
                }

                $data[] = $habitacionInfo;

                return $this->json([
                    'success' => true,
                    'content' => [$data]
                ], Response::HTTP_OK);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'There are no rooms',
                    'habitacion' => [],
                ]);
            }
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
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // Validar datos necesarios
            if (!isset($data['paciente_id'], $data['avd'], $data['o2'], $data['panales'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Faltan datos requeridos (paciente_id, avd, o2, panales)'
                ], Response::HTTP_BAD_REQUEST);
            }

            $paciente = $pacienteRepository->find($data['paciente_id']);
            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Crear Diagnostico básico
            $diagnostico = new \App\Entity\Diagnostico();
            $diagnostico->setPacienteId($paciente);
            $diagnostico->setFecha(new \DateTime());
            $diagnostico->setToma('Automática');

            // Simulación de auxiliar (puedes cambiarlo por autenticado o fijo)
            $auxiliar = $paciente->getHabitacion()->getAuxiliar(); // O cualquier lógica
            $diagnostico->setAuxiliarId($auxiliar);

            $em->persist($diagnostico);
            $em->flush();

            // Crear DetalleDiagnostico
            $detalle = new DetalleDiagnostico();
            $detalle->setDiagnosticoId($diagnostico);
            $detalle->setAvd($data['avd']);
            $detalle->setO2((int)$data['o2']);
            $detalle->setPanales((int)$data['panales']);

            $em->persist($detalle);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'DetalleDiagnostico creado con éxito',
                'detalle_diagnostico_id' => $detalle->getId()
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

    #[Route('/diets', name: 'api_all_diets', methods: ['GET'])]
    public function getAllDiets(
        EntityManagerInterface $entityManager,
        HabitacionRepository $habitacionRepository,
        RegistroRepository $registroRepository,
        DietaHasTipoDietaRepository $dietaHasTipoDietaRepository,
        TipoDietaRepository $tipoDietaRepository
    ): JsonResponse {
        try {
            // Obtener todas las habitaciones
            $habitaciones = $habitacionRepository->findAll();

            if (empty($habitaciones)) {
                return $this->json([
                    'success' => false,
                    'content' => [
                        'message' => 'No se encontraron habitaciones'
                    ]
                ], Response::HTTP_NOT_FOUND);
            }

            $formattedResults = [];

            foreach ($habitaciones as $habitacion) {
                $habitacionCodigo = $habitacion->getCodigo();

                // Buscar asignación de paciente a la habitación (paciente_has_habitaciones)
                $asignacion = $entityManager->getRepository('App\Entity\PacienteHasHabitaciones')
                    ->findOneBy(['habitacion_id' => $habitacion], ['timestamp' => 'DESC']);

                if (!$asignacion) {
                    // Habitación vacía
                    $formattedResults[] = [
                        'habitacion_codigo' => $habitacionCodigo,
                        'message' => 'empty room'
                    ];
                    continue;
                }

                // Obtener el paciente
                $paciente = $asignacion->getPacienteId(); // Ajustado de getPacienteIdId() a getPacienteId()

                // Calcular la edad del paciente
                $edad = $this->calcularEdad($paciente->getFechaNacimiento());

                // Buscar el registro más reciente del paciente
                $registro = $registroRepository->findOneBy(
                    ['paciente_id' => $paciente], // Esto depende de la entidad Registro
                    ['fecha' => 'DESC']
                );

                if (!$registro) {
                    // Si no hay registros, omitimos esta habitación
                    continue;
                }

                // Obtener los datos de dieta
                $dieta = $registro->getDietaId();

                // Obtener los tipos de dieta asociados
                $tipoDietaDescriptions = [];
                $dietaHasTipoDietas = $dietaHasTipoDietaRepository->findBy(['dieta_id' => $dieta->getId()]);

                foreach ($dietaHasTipoDietas as $relation) {
                    $tipoDietaId = $relation->getTipoDietaId(); // Esto depende de la entidad DietaHasTipoDieta
                    $tipoDieta = $tipoDietaRepository->find($tipoDietaId);

                    if ($tipoDieta) {
                        $tipoDietaDescriptions[] = $tipoDieta->getDescripcion();
                    }
                }

                $tipoDietaString = implode(', ', $tipoDietaDescriptions);

                // Formatear la respuesta
                $formattedResults[] = [
                    'habitacion_codigo' => $habitacionCodigo,
                    'paciente' => [
                        'nombre' => $paciente->getNombre(),
                        'apellidos' => $paciente->getApellidos(),
                        'edad' => $edad
                    ],
                    'detalle' => [
                        'tipo_dieta' => $tipoDietaString,
                        'textura' => $dieta->getTipoTexturaId()->getDescripcion(),
                        'protesis' => $dieta->getProtesi() ? 'Sí' : 'No',
                        'asistencia' => $dieta->getAutonomo() ? 'Independiente' : 'Dependiente',
                        'observaciones' => $registro->getObservacion()->getDescripcion()
                    ]
                ];
            }

            return $this->json([
                'success' => true,
                'content' => $formattedResults
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
        HabitacionRepository $habitacionRepository,
        PacienteRepository $pacienteRepository,
        DiagnosticoRepository $diagnosticoRepository,
        RegistroRepository $registroRepository,
        PacienteHasHabitacionesRepository $pacienteHasHabitacionesRepository
    ): JsonResponse {
        $habitaciones = $habitacionRepository->findAll();
        $data = [];

        if (!empty($habitaciones)) {

            foreach ($habitaciones as $habitacion) {
                $habitacionInfo = [
                    'habitacion_codigo' => $habitacion->getCodigo(),
                ];

                // Obtener el último paciente relacionado con la habitación
                $paciente = $pacienteHasHabitacionesRepository->findUltimoPacientePorHabitacion($habitacion);

                if (!$paciente) {
                    $habitacionInfo['isEmpty'] = true;
                } else {
                    $habitacionInfo['isEmpty'] = false;

                    // Obtener el último diagnóstico del paciente
                    $ultimoDiagnostico = $diagnosticoRepository->findUltimoDiagnosticoPorPaciente($paciente);

                    // Obtener el último registro del paciente
                    $ultimoRegistro = $registroRepository->findByUltimoPorPaciente($paciente);

                    // Detalles del paciente
                    $habitacionInfo['paciente'] = [
                        'nombre' => $paciente->getNombre(),
                        'apellidos' => $paciente->getApellidos(),
                        'edad' => $this->calcularEdad($paciente->getFechaNacimiento()),
                        'diagnostico' => $ultimoDiagnostico ? $ultimoDiagnostico->getDiagnostico() : null,
                    ];

                    // Detalles del último registro
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
            ]);
        } else {
            return $this->json([
                'success' => false,
                'message' => 'There are no rooms',
                'habitacion' => [],
            ]);
        }
    }
}
