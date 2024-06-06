<?php

namespace App\Repository;

use App\Entity\StatusSystemQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusSystemQueue>
 *
 * @method StatusSystemQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusSystemQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusSystemQueue[]    findAll()
 * @method StatusSystemQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusSystemQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusSystemQueue::class);
    }

    public function save(StatusSystemQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatusSystemQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatusSystemQueue[] Returns an array of StatusSystemQueue objects
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

//    public function findOneBySomeField($value): ?StatusSystemQueue
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
