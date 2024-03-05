<?php

namespace App\Repository\Customer;

use App\Entity\Customer\accordImportDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<accordImportDbi>
 *
 * @method accordImportDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method accordImportDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method accordImportDbi[]    findAll()
 * @method accordImportDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class accordImportDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, accordImportDbi::class);
    }

    public function save(accordImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(accordImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return accordImportDbi[] Returns an array of accordImportDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?accordImportDbi
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
