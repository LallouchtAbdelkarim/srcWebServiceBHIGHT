<?php

namespace App\Repository;

use App\Entity\StatusPromise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusPromise>
 *
 * @method StatusPromise|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusPromise|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusPromise[]    findAll()
 * @method StatusPromise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusPromiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusPromise::class);
    }

//    /**
//     * @return StatusPromise[] Returns an array of StatusPromise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatusPromise
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
