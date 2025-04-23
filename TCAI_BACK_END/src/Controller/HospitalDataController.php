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
    // #[Route('/hospital/data', name: 'app_hospital_data')]
    // public function index(): Response
    // {
    //     return $this->render('hospital_data/index.html.twig', [
    //         'controller_name' => 'HospitalDataController',
    //     ]);
    // }

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

    #[Route('/registro/paciente/{id}', name: 'create_registros_by_paciente', methods: ['POST'])]
    public function createRegistroByPaciente(#[MapRequestPayload] CreateRegistroDto $dto, EntityManagerInterface $entityManager, int $id, PacienteRepository $pacienteRepository, TipoTexturaRepository $tipoTexturaRepository, TipoDietaRepository $tipoDietaRepository, DietaHasTipoDietaRepository $dietaHasTipoDietaRepository, RegistroRepository $registroRepository): JsonResponse
    {
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

            // Parse JSON request body
            // $data = json_decode($request->getContent(), true);

            // if (!$data) {
            //     return $this->json([
            //         'success' => false,
            //         'content' => [
            //             'message' => 'Invalid JSON format'
            //         ]
            //     ], 400);
            // }

            // Validate required fields
            // if (!isset($data['paciente_id']) || !isset($data['registro'])) {
            //     return $this->json([
            //         'success' => false,
            //         'content' => [
            //             'message' => 'Missing required fields: paciente_id or registro'
            //         ]
            //     ], 400);
            // }

            // Process the data 

            // OBSERVACION
            $observacion = new Observacion();
            $observacion->setDescripcion($dto->observacion_descripcion);
            $entityManager->persist($observacion);

            // DIETA
            $dieta = new Dieta();
            $dieta->setAutonomo($dto->dieta_autonomo);
            $dieta->setProtesi($dto->dieta_protesi);
            // $dieta->setTipoTexturaId(); !!!
            $entityManager->persist($dieta);

            // Dieta Has Tipo Dieta

            // to make iteration !!!
            // $dietaHasTipoDieta = new DietaHasTipoDieta();
            // $dietaHasTipoDieta->setDietaId($dieta->getId());
            

            // DRENAJE
            // $drenaje = new Drenaje();
            // $drenaje->setDescripcion($dto->drenajeDescripcion);
            // $entityManager->persist($drenaje);

            // MOVILIZACIÓN
            // $movilizacion = new Movilizacion();
            // $movilizacion->setSedestacion($dto->sedestacion);
            // $movilizacion->setAyudaDeambulacion($dto->ayudaDeambulacion);
            // $movilizacion->setAyudaDescripcion($dto->ayudaDescripcion);
            // $movilizacion->setCambiosPosturales($dto->cambiosPosturales);
            // $entityManager->persist($movilizacion);

            // CONSTANTES VITALES
            // $constantesVitales = new ConstantesVitales();
            // $constantesVitales->setTaSistolica($dto->taSistolica);
            // $constantesVitales->setTaDiastolica($dto->taDiastolica);
            // $constantesVitales->setFrecuenciaRespiratoria($dto->frecuenciaRespiratoria);
            // $constantesVitales->setPulso($dto->pulso);
            // $constantesVitales->setTemperatura($dto->temperatura);
            // $constantesVitales->setSaturacionOxigeno($dto->saturacionOxigeno);
            // $entityManager->persist($constantesVitales);

            // BALANCE HIDRICO
            // $balanceHidrico = new BalanceHidrico();
            // $balanceHidrico->setDiuresis($dto->diuresis);
            // $balanceHidrico->setDeposicion($dto->deposicion);
            // $entityManager->persist($balanceHidrico);

            // SUEROTERAPIA
            // $sueroterapia = new Sueroterapia();
            // $sueroterapia->setDosis($dto->dosis);
            // $entityManager->persist($sueroterapia);

            // HIGIENE
            // $higiene = new Higiene();
            // $higiene->setDescripcion($dto->higieneDescripcion);
            // $entityManager->persist($higiene);

            // REGISTRO
            $registro = new Registro();

            // $registro->setFecha(new \DateTime());
            // $registro->setToma($dto->toma);

            // auxiliar: to get the auth user !!!

            // paciente
            $registro->setPacienteId($paciente);

            $registro->setObservacion($observacion);
            $registro->setDietaId($dieta);
            // $registro->setDrenajeId($drenaje);
            // $registro->setMovilizacionId($movilizacion);
            // $registro->setConstantesVitalesId($constantesVitales);
            // $registro->setBalanceHidricoId($balanceHidrico);
            // $registro->setSueroterapiaId($sueroterapia);
            // $registro->setHigieneId($higiene);
            $entityManager->persist($registro);

            // Flush all changes to the database
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'content' => [
                    'message' => 'Registro created successfully',
                    'observacion' => $dto->observacion_descripcion,
                    'dieta' => [
                        'autonomo' => $dto->dieta_autonomo,
                        'protesi' => $dto->dieta_protesi,
                    ],
                    // 'registro_id' => $registro->getId() !!!
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

    // #[Route('/product/edit/{id}', name: 'product_edit')]
    // public function update(EntityManagerInterface $entityManager, int $id): Response
    // {
    //     $product = $entityManager->getRepository(Product::class)->find($id);

    //     if (!$product) {
    //         throw $this->createNotFoundException(
    //             'No product found for id '.$id
    //         );
    //     }

    //     $product->setName('New product name!');
    //     $entityManager->flush();

    //     return $this->redirectToRoute('product_show', [
    //         'id' => $product->getId()
    //     ]);
    // }

    // https://www.adcisolutions.com/knowledge/getting-started-rest-api-symfony-4


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
                $fechaNacimiento = $paciente->getFechaNacimiento();
                $edad = $fechaNacimiento ? (new \DateTime())->diff($fechaNacimiento)->y : null;

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
    public function getAllRooms(HabitacionRepository $habitacionRepository): JsonResponse
    {
        $habitaciones = $habitacionRepository->findAll();

        if (!empty($habitaciones)) {

            foreach ($habitaciones as $habitacion) {
                $habitacionInfo = [
                    'habitacion_codigo' => $habitacion->getCodigo(),
                ];

                $pacientesRelacionados = $habitacion->getPacienteHasHabitaciones();

                if ($pacientesRelacionados->isEmpty()) {
                    $habitacionInfo['isEmpty'] = true;
                } else {
                    $habitacionInfo['isEmpty'] = false;

                    $paciente = $pacientesRelacionados->last()?->getPacienteId();

                    if ($paciente) {
                        $registros = $paciente->getRegistros()->toArray();

                        usort($registros, function ($a, $b) {
                            return $b->getFecha() <=> $a->getFecha();
                        });

                        // dd($registros);
                        $ultimoRegistro = $registros[0] ?? null;

                        $habitacionInfo['paciente'] = [
                            'nombre' => $paciente->getNombre(),
                            'apellidos' => $paciente->getApellidos(),
                            'edad' => $paciente->getFechaNacimiento() /* cambiar por calcular la edad*/,
                            'diagnostico' => $paciente->getDiagnostico()->last()->getDiagnostico(),
                        ];

                        if ($ultimoRegistro) {
                            $habitacionInfo['registro'] = [
                                'fecha' => $ultimoRegistro->getFecha()->format('Y-m-d H:i:s'),
                                'nombre_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNombre(),
                                'numero_auxiliar' => $ultimoRegistro->getAuxiliarId()->getNumTrabajador(),
                                'observaciones' => $ultimoRegistro->getObservacion()->getDescripcion(),
                                'alerta' => true/*arreglar mas tarde*/,
                            ];
                        } else {
                            $habitacionInfo['registro'] = null;
                        }
                    }
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
