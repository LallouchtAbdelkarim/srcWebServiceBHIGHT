<?php

namespace App\Repository;

use App\Entity\ActiviteAssigned;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActiviteAssigned>
 *
 * @method ActiviteAssigned|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActiviteAssigned|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActiviteAssigned[]    findAll()
 * @method ActiviteAssigned[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteAssignedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActiviteAssigned::class);
    }

//    /**
//     * @return ActiviteAssigned[] Returns an array of ActiviteAssigned objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActiviteAssigned
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
