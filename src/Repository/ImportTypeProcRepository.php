<?php

namespace App\Repository;

use App\Entity\ImportTypeProc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportTypeProc>
 *
 * @method ImportTypeProc|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportTypeProc|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportTypeProc[]    findAll()
 * @method ImportTypeProc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportTypeProcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportTypeProc::class);
    }

    public function save(ImportTypeProc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImportTypeProc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImportTypeProc[] Returns an array of ImportTypeProc objects
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

//    public function findOneBySomeField($value): ?ImportTypeProc
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
