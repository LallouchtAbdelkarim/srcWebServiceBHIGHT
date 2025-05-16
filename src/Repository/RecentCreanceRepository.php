<?php

namespace App\Repository;

use App\Entity\RecentCreance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecentCreance>
 *
 * @method RecentCreance|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecentCreance|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecentCreance[]    findAll()
 * @method RecentCreance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecentCreanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecentCreance::class);
    }

//    /**
//     * @return RecentCreance[] Returns an array of RecentCreance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RecentCreance
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
