<?php

namespace App\Repository;

use App\Entity\SegGroupeCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SegGroupeCritere>
 *
 * @method SegGroupeCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method SegGroupeCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method SegGroupeCritere[]    findAll()
 * @method SegGroupeCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SegGroupeCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SegGroupeCritere::class);
    }

    public function save(SegGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SegGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SegGroupeCritere[] Returns an array of SegGroupeCritere objects
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

//    public function findOneBySomeField($value): ?SegGroupeCritere
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
