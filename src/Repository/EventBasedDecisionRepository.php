<?php

namespace App\Repository;

use App\Entity\EventBasedDecision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventBasedDecision>
 *
 * @method EventBasedDecision|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventBasedDecision|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventBasedDecision[]    findAll()
 * @method EventBasedDecision[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventBasedDecisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventBasedDecision::class);
    }

    public function save(EventBasedDecision $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventBasedDecision $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EventBasedDecision[] Returns an array of EventBasedDecision objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EventBasedDecision
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
