<?php

namespace App\Repository;

use App\Entity\RegleModelFacturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegleModelFacturation>
 *
 * @method RegleModelFacturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegleModelFacturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegleModelFacturation[]    findAll()
 * @method RegleModelFacturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegleModelFacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegleModelFacturation::class);
    }

    public function save(RegleModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegleModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RegleModelFacturation[] Returns an array of RegleModelFacturation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegleModelFacturation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
