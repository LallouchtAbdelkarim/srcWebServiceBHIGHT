<?php

namespace App\Repository;

use App\Entity\ModelCourrier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModelCourrier>
 *
 * @method ModelCourrier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelCourrier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelCourrier[]    findAll()
 * @method ModelCourrier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelCourrierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelCourrier::class);
    }

//    /**
//     * @return ModelCourrier[] Returns an array of ModelCourrier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModelCourrier
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
