<?php

namespace App\Repository;

use App\Entity\QueueSplitDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QueueSplitDetails>
 *
 * @method QueueSplitDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueueSplitDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueueSplitDetails[]    findAll()
 * @method QueueSplitDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueSplitDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueSplitDetails::class);
    }

    public function save(QueueSplitDetails $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QueueSplitDetails $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return QueueSplitDetails[] Returns an array of QueueSplitDetails objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QueueSplitDetails
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
