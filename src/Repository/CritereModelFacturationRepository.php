<?php

namespace App\Repository;

use App\Entity\CritereModelFacturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CritereModelFacturation>
 *
 * @method CritereModelFacturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CritereModelFacturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CritereModelFacturation[]    findAll()
 * @method CritereModelFacturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CritereModelFacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CritereModelFacturation::class);
    }

    public function save(CritereModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CritereModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CritereModelFacturation[] Returns an array of CritereModelFacturation objects
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

//    public function findOneBySomeField($value): ?CritereModelFacturation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
