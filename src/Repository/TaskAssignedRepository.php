<?php

namespace App\Repository;

use App\Entity\TaskAssigned;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskAssigned>
 *
 * @method TaskAssigned|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskAssigned|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskAssigned[]    findAll()
 * @method TaskAssigned[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskAssignedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskAssigned::class);
    }

//    /**
//     * @return TaskAssigned[] Returns an array of TaskAssigned objects
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

//    public function findOneBySomeField($value): ?TaskAssigned
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
