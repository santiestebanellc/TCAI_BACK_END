<?php

namespace App\Repository;

use App\Entity\Observacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Observacion>
 */
class ObservacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Observacion::class);
    }

    //    /**
    //     * @return Observacion[] Returns an array of Observacion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Observacion
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
