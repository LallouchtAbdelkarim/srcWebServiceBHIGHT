<?php

namespace App\Repository;

use App\Entity\DetailsApprovalStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsApprovalStep>
 *
 * @method DetailsApprovalStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsApprovalStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsApprovalStep[]    findAll()
 * @method DetailsApprovalStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsApprovalStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsApprovalStep::class);
    }

    public function save(DetailsApprovalStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsApprovalStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsApprovalStep[] Returns an array of DetailsApprovalStep objects
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

//    public function findOneBySomeField($value): ?DetailsApprovalStep
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
