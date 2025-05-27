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
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;

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


    // POST
    #[Route('/paciente/registro/', name: 'create_registros_by_paciente', methods: ['POST'])]
    public function createRegistroByPaciente(
        Request $request,
        SerializerSerializerInterface $serializer,
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

            $registro = new Registro();
            $fecha = new \DateTime('now', new \DateTimeZone('UTC'));
            $fecha->setTimezone(new \DateTimeZone('Europe/Madrid'));
            $registro->setFecha($fecha);
            $registro->setToma(HospitalUtils::calcularToma($fecha));

            $registro->setAuxiliarId($auxiliar);
            $registro->setPacienteId($paciente);


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
                    'message' => $e->getMessage(),
                ]
            ], 400);
        }
    }
}
