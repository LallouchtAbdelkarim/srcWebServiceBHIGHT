<?php

namespace App\Repository;

use App\Entity\DetailCritereSegment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCritereSegment>
 *
 * @method DetailCritereSegment|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCritereSegment|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCritereSegment[]    findAll()
 * @method DetailCritereSegment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCritereSegmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCritereSegment::class);
    }

    public function save(DetailCritereSegment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailCritereSegment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailCritereSegment[] Returns an array of DetailCritereSegment objects
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

//    public function findOneBySomeField($value): ?DetailCritereSegment
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
