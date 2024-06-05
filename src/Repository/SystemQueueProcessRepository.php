<?php

namespace App\Repository;

use App\Entity\SystemQueueProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SystemQueueProcess>
 *
 * @method SystemQueueProcess|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemQueueProcess|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemQueueProcess[]    findAll()
 * @method SystemQueueProcess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemQueueProcessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemQueueProcess::class);
    }

    public function save(SystemQueueProcess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SystemQueueProcess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SystemQueueProcess[] Returns an array of SystemQueueProcess objects
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

//    public function findOneBySomeField($value): ?SystemQueueProcess
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
