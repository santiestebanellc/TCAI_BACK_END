<?php

namespace App\Controller;

use App\Dto\CreateRegistroDto;
use App\Dto\RegistroInput;
use App\Entity\Auxiliar;
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
use App\Entity\Paciente;
use App\Entity\Sueroterapia;
use App\Entity\TipoDieta;
use App\Entity\TipoHigiene;
use App\Entity\TipoTextura;
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
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

            // AUXILIAR AND PACIENTE
            // $auxiliar = $em->getRepository(Auxiliar::class)->findOneBy([
            //     'numTrabajador' => $this->getUser()->getUserIdentifier()
            // ]);
            // $registro->setAuxiliar($auxiliar);

            // // paciente from query parameter (or route param)
            // $pacienteId = $request->query->get('pacienteId');
            // $registro->setPaciente($em->getReference(Paciente::class, $pacienteId));

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

    // https://www.adcisolutions.com/knowledge/getting-started-rest-api-symfony-4


}