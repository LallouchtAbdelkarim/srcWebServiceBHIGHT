<?php

namespace App\Repository;

use App\Entity\DetailCritereModelFacturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCritereModelFacturation>
 *
 * @method DetailCritereModelFacturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCritereModelFacturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCritereModelFacturation[]    findAll()
 * @method DetailCritereModelFacturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCritereModelFacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCritereModelFacturation::class);
    }

    public function save(DetailCritereModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailCritereModelFacturation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailCritereModelFacturation[] Returns an array of DetailCritereModelFacturation objects
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

//    public function findOneBySomeField($value): ?DetailCritereModelFacturation
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
