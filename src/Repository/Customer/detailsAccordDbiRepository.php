<?php

namespace App\Repository\Customer;

use App\Entity\Customer\detailsAccordDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<detailsAccordDbi>
 *
 * @method detailsAccordDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method detailsAccordDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method detailsAccordDbi[]    findAll()
 * @method detailsAccordDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class detailsAccordDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, detailsAccordDbi::class);
    }

    public function save(detailsAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(detailsAccordDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return detailsAccordDbi[] Returns an array of detailsAccordDbi objects
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

//    public function findOneBySomeField($value): ?detailsAccordDbi
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
