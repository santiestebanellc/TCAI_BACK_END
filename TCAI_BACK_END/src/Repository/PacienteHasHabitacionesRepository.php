<?php

namespace App\Repository;

use App\Entity\PacienteHasHabitaciones;
use App\Entity\Habitacion;
use App\Entity\Paciente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PacienteHasHabitaciones>
 */
class PacienteHasHabitacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PacienteHasHabitaciones::class);
    }


    public function findUltimoPacientePorHabitacion(Habitacion $habitacion): ?Paciente
    {
        $phh = $this->createQueryBuilder('phh')
            ->andWhere('phh.habitacion_id = :habitacion')
            ->setParameter('habitacion', $habitacion)
            ->orderBy('phh.timestamp', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $phh?->getPacienteId();
    }

    //    /**
    //     * @return PacienteHasHabitaciones[] Returns an array of PacienteHasHabitaciones objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PacienteHasHabitaciones
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
