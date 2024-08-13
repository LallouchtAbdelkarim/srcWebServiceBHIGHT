<?php

namespace App\Repository;

use App\Entity\ColumnModelExport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ColumnModelExport>
 *
 * @method ColumnModelExport|null find($id, $lockMode = null, $lockVersion = null)
 * @method ColumnModelExport|null findOneBy(array $criteria, array $orderBy = null)
 * @method ColumnModelExport[]    findAll()
 * @method ColumnModelExport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColumnModelExportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ColumnModelExport::class);
    }

//    /**
//     * @return ColumnModelExport[] Returns an array of ColumnModelExport objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ColumnModelExport
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
