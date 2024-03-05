<?php

namespace App\Repository\Customer;

use App\Entity\Customer\procDebiteurDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<procDebiteurDbi>
 *
 * @method procDebiteurDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method procDebiteurDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method procDebiteurDbi[]    findAll()
 * @method procDebiteurDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class procDebiteurDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, procDebiteurDbi::class);
    }

    public function save(procDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(procDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return procDebiteurDbi[] Returns an array of procDebiteurDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?procDebiteurDbi
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
