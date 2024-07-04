<?php

namespace App\Repository;

use App\Entity\BackgroundCourrier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BackgroundCourrier>
 *
 * @method BackgroundCourrier|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackgroundCourrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackgroundCourrier[]    findAll()
 * @method BackgroundCourrier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackgroundCourrierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackgroundCourrier::class);
    }

//    /**
//     * @return BackgroundCourrier[] Returns an array of BackgroundCourrier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BackgroundCourrier
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
