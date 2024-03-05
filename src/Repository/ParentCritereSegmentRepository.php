<?php

namespace App\Repository;

use App\Entity\ParentCritereSegment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParentCritereSegment>
 *
 * @method ParentCritereSegment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParentCritereSegment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParentCritereSegment[]    findAll()
 * @method ParentCritereSegment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParentCritereSegmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParentCritereSegment::class);
    }

    public function save(ParentCritereSegment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ParentCritereSegment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ParentCritereSegment[] Returns an array of ParentCritereSegment objects
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

//    public function findOneBySomeField($value): ?ParentCritereSegment
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
