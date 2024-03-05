<?php

namespace App\Repository;

use App\Entity\SplitGroupeCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SplitGroupeCritere>
 *
 * @method SplitGroupeCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method SplitGroupeCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method SplitGroupeCritere[]    findAll()
 * @method SplitGroupeCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SplitGroupeCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SplitGroupeCritere::class);
    }

    public function save(SplitGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SplitGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SplitGroupeCritere[] Returns an array of SplitGroupeCritere objects
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

//    public function findOneBySomeField($value): ?SplitGroupeCritere
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
