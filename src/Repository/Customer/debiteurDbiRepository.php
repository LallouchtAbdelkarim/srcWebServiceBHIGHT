<?php

namespace App\Repository\Customer;

use App\Entity\Customer\debiteurDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<debiteurDbi>
 *
 * @method debiteurDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method debiteurDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method debiteurDbi[]    findAll()
 * @method debiteurDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class debiteurDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, debiteurDbi::class);
    }

    public function save(debiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(debiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return debiteurDbi[] Returns an array of debiteurDbi objects
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

//    public function findOneBySomeField($value): ?debiteurDbi
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
