<?php

namespace App\Repository;

use App\Entity\Paciente;
use App\Entity\Habitacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paciente>
 */
class PacienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paciente::class);
    }

    public function findPacienteActualPorHabitacion(Habitacion $habitacion): ?Paciente
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM App\Entity\Paciente p
             JOIN App\Entity\PacienteHasHabitaciones phh
             WITH phh.paciente_id = p.id
             WHERE phh.habitacion_id = :habitacion
             ORDER BY phh.timestamp DESC'
            )
            ->setParameter('habitacion', $habitacion)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Paciente[] Returns an array of Paciente objects
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

    //    public function findOneBySomeField($value): ?Paciente
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
