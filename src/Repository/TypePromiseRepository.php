<?php

namespace App\Repository;

use App\Entity\TypePromise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypePromise>
 *
 * @method TypePromise|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypePromise|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypePromise[]    findAll()
 * @method TypePromise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePromiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypePromise::class);
    }

//    /**
//     * @return TypePromise[] Returns an array of TypePromise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypePromise
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
