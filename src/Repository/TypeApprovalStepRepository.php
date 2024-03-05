<?php

namespace App\Repository;

use App\Entity\TypeApprovalStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeApprovalStep>
 *
 * @method TypeApprovalStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeApprovalStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeApprovalStep[]    findAll()
 * @method TypeApprovalStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeApprovalStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeApprovalStep::class);
    }

    public function save(TypeApprovalStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeApprovalStep $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TypeApprovalStep[] Returns an array of TypeApprovalStep objects
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

//    public function findOneBySomeField($value): ?TypeApprovalStep
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
