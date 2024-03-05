<?php

namespace App\Repository;

use App\Entity\DetailsAppelCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsAppelCustomer>
 *
 * @method DetailsAppelCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsAppelCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsAppelCustomer[]    findAll()
 * @method DetailsAppelCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsAppelCustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsAppelCustomer::class);
    }

    public function save(DetailsAppelCustomer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsAppelCustomer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsAppelCustomer[] Returns an array of DetailsAppelCustomer objects
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

//    public function findOneBySomeField($value): ?DetailsAppelCustomer
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
