<?php

namespace App\Repository;

use App\Entity\Diagnostico;
use App\Entity\Paciente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Diagnostico>
 */
class DiagnosticoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diagnostico::class);
    }

    public function findUltimoDiagnosticoPorPaciente(Paciente $paciente): ?Diagnostico
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT d 
             FROM App\Entity\Diagnostico d
             WHERE d.paciente_id = :paciente
             ORDER BY d.fecha DESC'
            )
            ->setParameter('paciente', $paciente)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
    //    /**
    //     * @return Diagnostico[] Returns an array of Diagnostico objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Diagnostico
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
