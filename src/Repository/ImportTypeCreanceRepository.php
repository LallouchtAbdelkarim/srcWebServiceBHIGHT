<?php

namespace App\Repository;

use App\Entity\ImportTypeCreance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportTypeCreance>
 *
 * @method ImportTypeCreance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportTypeCreance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportTypeCreance[]    findAll()
 * @method ImportTypeCreance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportTypeCreanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportTypeCreance::class);
    }

    public function save(ImportTypeCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImportTypeCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImportTypeCreance[] Returns an array of ImportTypeCreance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ImportTypeCreance
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
