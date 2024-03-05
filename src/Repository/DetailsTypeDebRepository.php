<?php

namespace App\Repository;

use App\Entity\DetailsTypeDeb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsTypeDeb>
 *
 * @method DetailsTypeDeb|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsTypeDeb|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsTypeDeb[]    findAll()
 * @method DetailsTypeDeb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsTypeDebRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsTypeDeb::class);
    }

    public function save(DetailsTypeDeb $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsTypeDeb $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsTypeDeb[] Returns an array of DetailsTypeDeb objects
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

//    public function findOneBySomeField($value): ?DetailsTypeDeb
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
