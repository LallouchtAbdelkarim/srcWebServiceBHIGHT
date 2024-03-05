<?php

namespace App\Repository\Customer;

use App\Entity\Customer\detailCreanceDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<detailCreanceDbi>
 *
 * @method detailCreanceDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method detailCreanceDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method detailCreanceDbi[]    findAll()
 * @method detailCreanceDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class detailCreanceDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, detailCreanceDbi::class);
    }

    public function save(detailCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(detailCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return detailCreanceDbi[] Returns an array of detailCreanceDbi objects
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

//    public function findOneBySomeField($value): ?detailCreanceDbi
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
