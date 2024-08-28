<?php

namespace App\Repository;

use App\Entity\LogsActionsSegmentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogsActionsSegmentation>
 *
 * @method LogsActionsSegmentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogsActionsSegmentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogsActionsSegmentation[]    findAll()
 * @method LogsActionsSegmentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogsActionsSegmentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogsActionsSegmentation::class);
    }

//    /**
//     * @return LogsActionsSegmentation[] Returns an array of LogsActionsSegmentation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogsActionsSegmentation
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
