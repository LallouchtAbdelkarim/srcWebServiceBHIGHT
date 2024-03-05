<?php

namespace App\Repository;

use App\Entity\SplitFlowStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SplitFlowStep>
 *
 * @method SplitFlowStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method SplitFlowStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method SplitFlowStep[]    findAll()
 * @method SplitFlowStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SplitFlowStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SplitFlowStep::class);
    }

    public function save(SplitFlowStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SplitFlowStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SplitFlowStep[] Returns an array of SplitFlowStep objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SplitFlowStep
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
