<?php

namespace App\Repository;

use App\Entity\CritereParentSeg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CritereParentSeg>
 *
 * @method CritereParentSeg|null find($id, $lockMode = null, $lockVersion = null)
 * @method CritereParentSeg|null findOneBy(array $criteria, array $orderBy = null)
 * @method CritereParentSeg[]    findAll()
 * @method CritereParentSeg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CritereParentSegRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CritereParentSeg::class);
    }

    public function save(CritereParentSeg $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CritereParentSeg $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CritereParentSeg[] Returns an array of CritereParentSeg objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CritereParentSeg
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
