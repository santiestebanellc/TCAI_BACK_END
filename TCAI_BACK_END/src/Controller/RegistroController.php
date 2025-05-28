<?php

namespace App\Controller;

//Others
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

//Data Transfer Objects
use App\Dto\RegistroInput;

// Entities
use App\Entity\BalanceHidrico;
use App\Entity\ConstantesVitales;
use App\Entity\Dieta;
use App\Entity\DietaHasTipoDieta;
use App\Entity\Drenaje;
use App\Entity\Higiene;
use App\Entity\Movilizacion;
use App\Entity\Observacion;
use App\Entity\Registro;
use App\Entity\Sueroterapia;
use App\Entity\TipoDieta;
use App\Entity\TipoHigiene;
use App\Entity\TipoTextura;

//Repositories
use App\Repository\AuxiliarRepository;
use App\Repository\DietaHasTipoDietaRepository;
use App\Repository\PacienteRepository;
use App\Repository\RegistroRepository;
use App\Repository\TipoDietaRepository;

final class RegistroController extends AbstractController
{
    // GET
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
    public function getRegistrosByPaciente(
        int $id,
        RegistroRepository $registroRepository,
        PacienteRepository $pacienteRepository
    ): JsonResponse {
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

    #[Route('/registro/{id}', name: 'api_registro_by_id', methods: ['GET'])]
public function getRegistroById(
    int $id,
    RegistroRepository $registroRepository,
    DietaHasTipoDietaRepository $dietaHasTipoDietaRepository,
    TipoDietaRepository $tipoDietaRepository
): JsonResponse {
    try {
        $registro = $registroRepository->find($id);

        if (!$registro) {
            return $this->json([
                'success' => false,
                'message' => 'Registro not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Helper function to safely get nested properties
        $safeGet = function($object, $method, $default = null) {
            return $object ? $object->$method() : $default;
        };

        // Build constantes vitales data
        $constantesVitales = null;
        if ($registro->getConstantesVitalesId()) {
            $cv = $registro->getConstantesVitalesId();
            $constantesVitales = [
                'ta_sistolica' => $cv->getTaSistolica(),
                'ta_diastolica' => $cv->getTaDiastolica(),
                'frecuencia_respiratoria' => $cv->getFrecuenciaRespiratoria(),
                'pulso' => $cv->getPulso(),
                'temperatura' => $cv->getTemperatura(),
                'saturacion_oxigeno' => $cv->getSaturacionOxigeno()
            ];
        }

        // Build dieta data
        $dietaData = null;
        if ($registro->getDietaId()) {
            $dieta = $registro->getDietaId();
            
            // Get tipo dieta descriptions
            $tipoDietaDescriptions = [];
            try {
                $dietaHasTipoDietas = $dietaHasTipoDietaRepository->findBy(['dieta_id' => $dieta->getId()]);
                
                foreach ($dietaHasTipoDietas as $relation) {
                    $tipoDietaId = $relation->getTipoDietaId();
                    if ($tipoDietaId) {
                        $tipoDieta = $tipoDietaRepository->find($tipoDietaId);
                        if ($tipoDieta) {
                            $tipoDietaDescriptions[] = $tipoDieta->getDescripcion();
                        }
                    }
                }
            } catch (\Exception $e) {
                // If there's an error getting diet types, continue with empty array
                $tipoDietaDescriptions = [];
            }

            $tipoDietaString = !empty($tipoDietaDescriptions) ? implode(', ', $tipoDietaDescriptions) : null;

            $dietaData = [
                'autonomo' => $dieta->getAutonomo(),
                'protesi' => $dieta->getProtesi(),
                'tipo_dieta' => $tipoDietaString,
                'tipo_textura' => $safeGet($dieta->getTipoTexturaId(), 'getDescripcion'),
            ];
        }

        // Build sueroterapia data
        $sueroterapiaData = null;
        if ($registro->getSueroterapiaId()) {
            $sueroterapiaData = $registro->getSueroterapiaId()->getDosis();
        }

        // Build balance hidrico data
        $balanceHidricoData = null;
        if ($registro->getBalanceHidricoId()) {
            $bh = $registro->getBalanceHidricoId();
            $balanceHidricoData = [
                'diuresis' => $bh->getDiuresis(),
                'deposicion' => $bh->getDeposicion(),
            ];
        }

        // Build drenaje data
        $drenajeData = null;
        if ($registro->getDrenajeId()) {
            $drenajeData = $registro->getDrenajeId()->getDescripcion();
        }

        // Build movilizacion data
        $movilizacionData = null;
        if ($registro->getMovilizacionId()) {
            $mov = $registro->getMovilizacionId();
            $movilizacionData = [
                'sedestacion' => $mov->getSedestacion(),
                'ayuda_deambulacion' => $mov->getAyudaDeambulacion(),
                'ayuda_descripcion' => $mov->getAyudaDescripcion(),
                'cambios_posturales' => $mov->getCambiosPosturales(),
            ];
        }

        // Build higiene data
        $higieneData = null;
        if ($registro->getHigieneId()) {
            $higiene = $registro->getHigieneId();
            $higieneData = [
                'tipo_higiene' => $safeGet($higiene->getTipo(), 'getDescripcion'),
                'higiene_descripcion' => $higiene->getDescripcion(),
            ];
        }

        // Build observacion data
        $observacionData = null;
        if ($registro->getObservacion()) {
            $observacionData = $registro->getObservacion()->getDescripcion();
        }

        // Build auxiliar data
        $auxiliarData = null;
        if ($registro->getAuxiliarId()) {
            $auxiliar = $registro->getAuxiliarId();
            $auxiliarData = [
                'nombre' => $auxiliar->getNombre(),
                'num_trabajador' => $auxiliar->getNumTrabajador()
            ];
        }

        $responseData = [
            'registro' => [
                'id' => $registro->getId(),
                'fecha' => $registro->getFecha() ? $registro->getFecha()->format('Y-m-d H:i:s') : null,
                'toma' => $registro->getToma(),
                'constantes_vitales' => $constantesVitales,
                'dieta' => $dietaData,
                'sueroterapia' => $sueroterapiaData,
                'balance_hidrico' => $balanceHidricoData,
                'drenaje' => $drenajeData,
                'movilizacion' => $movilizacionData,
                'higiene' => $higieneData,
                'observacion' => $observacionData,
                'auxiliar' => $auxiliarData,
                'paciente' => [
                    'id' => $safeGet($registro->getPacienteId(), 'getId'),
                    'nombre' => $safeGet($registro->getPacienteId(), 'getNombre'),
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


    // POST
    #[Route('/paciente/registro/', name: 'create_registros_by_paciente', methods: ['POST'])]
    public function createRegistroByPaciente(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        PacienteRepository $pacienteRepository,
        AuxiliarRepository $auxiliarRepository
    ): JsonResponse {
        try {
            $data = $request->getContent();
            $registroInput = $serializer->deserialize($data, RegistroInput::class, 'json');
            $errors = $validator->validate($registroInput);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $data = json_decode($request->getContent(), true);

            // Get paciente
            $paciente = $pacienteRepository->find($data['paciente_id'] ?? null);
            if (!$paciente) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Get auxiliar
            $auxiliar = $auxiliarRepository->find($data['auxiliar_id'] ?? null);
            if (!$auxiliar) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Auxiliar no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            $registro = new Registro();
            $fecha = new \DateTime('now', new \DateTimeZone('UTC'));
            $fecha->setTimezone(new \DateTimeZone('Europe/Madrid'));
            $registro->setFecha($fecha);
            $registro->setToma(HospitalUtils::calcularToma($fecha));
            $registro->setAuxiliarId($auxiliar);
            $registro->setPacienteId($paciente);

            // Observacion
            if ($registroInput->observacion && !empty(trim($registroInput->observacion->descripcion))) {
                $obs = new Observacion();
                $obs->setDescripcion($registroInput->observacion->descripcion);
                $em->persist($obs);
                $registro->setObservacion($obs);
            }

            // Dieta
            if ($registroInput->dieta) {
                $hasDietaData =
                    $registroInput->dieta->autonomo !== null ||
                    $registroInput->dieta->protesi !== null ||
                    $registroInput->dieta->tipoTexturaId !== null ||
                    (!empty($registroInput->dieta->tipoDietaId) && is_array($registroInput->dieta->tipoDietaId));

                if ($hasDietaData) {
                    $dieta = new Dieta();
                    $dieta->setAutonomo($registroInput->dieta->autonomo);
                    $dieta->setProtesi($registroInput->dieta->protesi);

                    // Set tipoTexturaId only if valid
                    if ($registroInput->dieta->tipoTexturaId !== null) {
                        $tipoTextura = $em->getReference(TipoTextura::class, $registroInput->dieta->tipoTexturaId);
                        $dieta->setTipoTexturaId($tipoTextura);
                    }

                    $em->persist($dieta);

                    // Handle tipoDietaId
                    if (!empty($registroInput->dieta->tipoDietaId) && is_array($registroInput->dieta->tipoDietaId)) {
                        foreach ($registroInput->dieta->tipoDietaId as $tipo) {
                            $tipoDieta = new DietaHasTipoDieta();
                            $tipoDieta->setDietaId($dieta);
                            $tipoDieta->setTipoDietaId($em->getReference(TipoDieta::class, $tipo));
                            $em->persist($tipoDieta);
                        }
                    }

                    $registro->setDietaId($dieta);
                }
            }

            // Drenaje
            if ($registroInput->drenaje && !empty(trim($registroInput->drenaje->descripcion))) {
                $dren = new Drenaje();
                $dren->setDescripcion($registroInput->drenaje->descripcion);
                $em->persist($dren);
                $registro->setDrenajeId($dren);
            }

            // Higiene
            if ($registroInput->higiene && ($registroInput->higiene->tipoId !== null || !empty(trim($registroInput->higiene->descripcion)))) {
                $hig = new Higiene();
                $hig->setDescripcion($registroInput->higiene->descripcion);
                if ($registroInput->higiene->tipoId !== null) {
                    $hig->setTipo($em->getReference(TipoHigiene::class, $registroInput->higiene->tipoId));
                }
                $em->persist($hig);
                $registro->setHigieneId($hig);
            }

            // Constantes Vitales
            if ($registroInput->constantesVitales) {
                $hasCVData =
                    $registroInput->constantesVitales->taSistolica !== null ||
                    $registroInput->constantesVitales->taDiastolica !== null ||
                    $registroInput->constantesVitales->frecuenciaRespiratoria !== null ||
                    $registroInput->constantesVitales->pulso !== null ||
                    $registroInput->constantesVitales->temperatura !== null ||
                    $registroInput->constantesVitales->saturacionOxigeno !== null;

                if ($hasCVData) {
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
            }

            // Movilización
            if ($registroInput->movilizacion) {
                $hasMovData =
                    !empty(trim($registroInput->movilizacion->sedestacion)) ||
                    $registroInput->movilizacion->ayudaDeambulacion !== null ||
                    !empty(trim($registroInput->movilizacion->ayudaDescripcion)) ||
                    !empty(trim($registroInput->movilizacion->cambiosPosturales));

                if ($hasMovData) {
                    $mov = new Movilizacion();
                    $mov->setSedestacion($registroInput->movilizacion->sedestacion);
                    $mov->setAyudaDeambulacion($registroInput->movilizacion->ayudaDeambulacion);
                    $mov->setAyudaDescripcion($registroInput->movilizacion->ayudaDescripcion);
                    $mov->setCambiosPosturales($registroInput->movilizacion->cambiosPosturales);
                    $em->persist($mov);
                    $registro->setMovilizacionId($mov);
                }
            }

            // Sueroterapia
            if ($registroInput->sueroterapia && $registroInput->sueroterapia->dosis !== null) {
                $stp = new Sueroterapia();
                $stp->setDosis($registroInput->sueroterapia->dosis);
                $em->persist($stp);
                $registro->setSueroterapiaId($stp);
            }

            // Balance Hidrico
            if ($registroInput->balanceHidrico) {
                $hasBHData =
                    $registroInput->balanceHidrico->diuresis !== null ||
                    !empty(trim($registroInput->balanceHidrico->deposicion));

                if ($hasBHData) {
                    $bh = new BalanceHidrico();
                    $bh->setDiuresis($registroInput->balanceHidrico->diuresis);
                    $bh->setDeposicion($registroInput->balanceHidrico->deposicion);
                    $em->persist($bh);
                    $registro->setBalanceHidricoId($bh);
                }
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
                    'message' => $e->getMessage(),
                ]
            ], 400);
        }
    }
}