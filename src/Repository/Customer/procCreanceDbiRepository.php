<?php

namespace App\Repository\Customer;

use App\Entity\Customer\procCreanceDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<procCreanceDbi>
 *
 * @method procCreanceDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method procCreanceDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method procCreanceDbi[]    findAll()
 * @method procCreanceDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class procCreanceDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, procCreanceDbi::class);
    }

    public function save(procCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(procCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return procCreanceDbi[] Returns an array of procCreanceDbi objects
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

//    public function findOneBySomeField($value): ?procCreanceDbi
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
