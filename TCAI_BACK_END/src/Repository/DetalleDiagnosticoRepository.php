<?php

namespace App\Repository;

use App\Entity\DetalleDiagnostico;
use App\Entity\Diagnostico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetalleDiagnostico>
 */
class DetalleDiagnosticoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetalleDiagnostico::class);
    }

    public function findUltimoPorDiagnostico(Diagnostico $diagnostico): ?DetalleDiagnostico
    {
        return $this->createQueryBuilder('d')
            ->join('d.diagnostico_id', 'diag')  // Hacemos un JOIN con la entidad Diagnostico
            ->where('d.diagnostico_id = :diagnostico')
            ->setParameter('diagnostico', $diagnostico)
            ->orderBy('diag.fecha', 'DESC')  // Ordenamos por la fecha en Diagnostico
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return DetalleDiagnostico[] Returns an array of DetalleDiagnostico objects
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

    //    public function findOneBySomeField($value): ?DetalleDiagnostico
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
