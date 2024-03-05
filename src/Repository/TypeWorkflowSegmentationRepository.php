<?php

namespace App\Repository;

use App\Entity\TypeWorkflowSegmentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeWorkflowSegmentation>
 *
 * @method TypeWorkflowSegmentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeWorkflowSegmentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeWorkflowSegmentation[]    findAll()
 * @method TypeWorkflowSegmentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeWorkflowSegmentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeWorkflowSegmentation::class);
    }

    public function save(TypeWorkflowSegmentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeWorkflowSegmentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TypeWorkflowSegmentation[] Returns an array of TypeWorkflowSegmentation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeWorkflowSegmentation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
