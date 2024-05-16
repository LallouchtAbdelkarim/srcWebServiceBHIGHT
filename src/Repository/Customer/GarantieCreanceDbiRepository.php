<?php

namespace App\Repository\Customer;

use App\Entity\Customer\GarantieCreanceDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GarantieCreanceDbi>
 *
 * @method GarantieCreanceDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method GarantieCreanceDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method GarantieCreanceDbi[]    findAll()
 * @method GarantieCreanceDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarantieCreanceDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GarantieCreanceDbi::class);
    }

    public function save(GarantieCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GarantieCreanceDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GarantieCreanceDbi[] Returns an array of GarantieCreanceDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GarantieCreanceDbi
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
