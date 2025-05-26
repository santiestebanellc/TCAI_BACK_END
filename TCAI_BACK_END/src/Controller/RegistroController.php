<?php

namespace App\Controller;

use App\Dto\RegistroInput;
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
use App\Form\RegistroType;
use App\Repository\AuxiliarRepository;
use App\Repository\PacienteRepository;
use App\Repository\RegistroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;

final class RegistroController extends AbstractController
{
    // GET

    #[Route(path: 'paciente/{pacienteId}/registro/{id}', name: 'app_registro_index', methods: ['GET'])]
    public function getRegistroById(RegistroRepository $registroRepository, $pacienteId, $id): JsonResponse
    {
        try {
            $registro = $registroRepository->findBy(
                ['paciente_id' => $pacienteId, 'id' => $id],
            );

            if ($registro) {
                return $this->json([
                    'success' => true,
                    'content' => $registro
                ], Response::HTTP_OK);
            } else {
                return $this->json([
                    'success' => true,
                    'content' => ['message' => "No s'ha trobat cap registre amb aquest id"]
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/paciente/registro/historia/{pacienteId}', name: 'app_registro_index', methods: ['GET'])]
    public function getHistoriaRegistrosByPacienteId(RegistroRepository $registroRepository, $pacienteId): JsonResponse
    {
        try {
            $registros = $registroRepository->findBy(
                ['paciente_id' => $pacienteId],
                ['fecha' => 'DESC'],
                15
            );

            if ($registros) {
                return $this->json([
                    'success' => true,
                    'content' => $registros
                ], Response::HTTP_OK);
            } else {
                return $this->json([
                    'success' => true,
                    'content' => ['message' => "El pacient amb id" . $pacienteId . "no te cap registre."]
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'content' => ['message' => $e->getMessage()]
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
            $registro->setToma($this->calcularToma($fecha));

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


    /* OTRAS FUNCIONES */

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
}
