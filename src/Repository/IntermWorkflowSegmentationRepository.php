<?php

namespace App\Repository;

use App\Entity\IntermWorkflowSegmentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntermWorkflowSegmentation>
 *
 * @method IntermWorkflowSegmentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntermWorkflowSegmentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntermWorkflowSegmentation[]    findAll()
 * @method IntermWorkflowSegmentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntermWorkflowSegmentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntermWorkflowSegmentation::class);
    }

    public function save(IntermWorkflowSegmentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntermWorkflowSegmentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntermWorkflowSegmentation[] Returns an array of IntermWorkflowSegmentation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IntermWorkflowSegmentation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
