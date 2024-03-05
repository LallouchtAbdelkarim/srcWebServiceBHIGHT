<?php

namespace App\Repository;

use App\Entity\DetailsTransferStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsTransferStep>
 *
 * @method DetailsTransferStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsTransferStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsTransferStep[]    findAll()
 * @method DetailsTransferStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsTransferStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsTransferStep::class);
    }

    public function save(DetailsTransferStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsTransferStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsTransferStep[] Returns an array of DetailsTransferStep objects
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

//    public function findOneBySomeField($value): ?DetailsTransferStep
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
