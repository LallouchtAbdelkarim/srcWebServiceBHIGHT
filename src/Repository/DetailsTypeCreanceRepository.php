<?php

namespace App\Repository;

use App\Entity\DetailsTypeCreance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsTypeCreance>
 *
 * @method DetailsTypeCreance|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsTypeCreance|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsTypeCreance[]    findAll()
 * @method DetailsTypeCreance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsTypeCreanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsTypeCreance::class);
    }

    public function save(DetailsTypeCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsTypeCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsTypeCreance[] Returns an array of DetailsTypeCreance objects
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

//    public function findOneBySomeField($value): ?DetailsTypeCreance
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
