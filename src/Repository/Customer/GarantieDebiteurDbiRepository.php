<?php

namespace App\Repository\Customer;

use App\Entity\Customer\GarantieDebiteurDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GarantieDebiteurDbi>
 *
 * @method GarantieDebiteurDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method GarantieDebiteurDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method GarantieDebiteurDbi[]    findAll()
 * @method GarantieDebiteurDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarantieDebiteurDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GarantieDebiteurDbi::class);
    }

    public function save(GarantieDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GarantieDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GarantieDebiteurDbi[] Returns an array of GarantieDebiteurDbi objects
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

//    public function findOneBySomeField($value): ?GarantieDebiteurDbi
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
