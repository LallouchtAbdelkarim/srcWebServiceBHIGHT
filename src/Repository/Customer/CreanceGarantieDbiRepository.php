<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CreanceGarantieDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreanceGarantieDbi>
 *
 * @method CreanceGarantieDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreanceGarantieDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreanceGarantieDbi[]    findAll()
 * @method CreanceGarantieDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreanceGarantieDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreanceGarantieDbi::class);
    }

    public function save(CreanceGarantieDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CreanceGarantieDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CreanceGarantieDbi[] Returns an array of CreanceGarantieDbi objects
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

//    public function findOneBySomeField($value): ?CreanceGarantieDbi
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
