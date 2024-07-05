<?php

namespace App\Repository;

use App\Entity\DetailMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailMission>
 *
 * @method DetailMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailMission[]    findAll()
 * @method DetailMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailMission::class);
    }

//    /**
//     * @return DetailMission[] Returns an array of DetailMission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailMission
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
