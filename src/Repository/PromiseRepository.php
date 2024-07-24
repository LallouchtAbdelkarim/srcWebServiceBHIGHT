<?php

namespace App\Repository;

use App\Entity\Promise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Promise>
 *
 * @method Promise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promise[]    findAll()
 * @method Promise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promise::class);
    }

//    /**
//     * @return Promise[] Returns an array of Promise objects
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

//    public function findOneBySomeField($value): ?Promise
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
