<?php

namespace App\Repository;

use App\Entity\SplitValuesCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SplitValuesCritere>
 *
 * @method SplitValuesCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method SplitValuesCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method SplitValuesCritere[]    findAll()
 * @method SplitValuesCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SplitValuesCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SplitValuesCritere::class);
    }

    public function save(SplitValuesCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SplitValuesCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SplitValuesCritere[] Returns an array of SplitValuesCritere objects
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

//    public function findOneBySomeField($value): ?SplitValuesCritere
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
