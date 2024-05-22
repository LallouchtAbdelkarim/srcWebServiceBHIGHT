<?php

namespace App\Repository;

use App\Entity\StatusQueueEventUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusQueueEventUser>
 *
 * @method StatusQueueEventUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusQueueEventUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusQueueEventUser[]    findAll()
 * @method StatusQueueEventUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusQueueEventUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusQueueEventUser::class);
    }

    public function save(StatusQueueEventUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatusQueueEventUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatusQueueEventUser[] Returns an array of StatusQueueEventUser objects
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

//    public function findOneBySomeField($value): ?StatusQueueEventUser
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
