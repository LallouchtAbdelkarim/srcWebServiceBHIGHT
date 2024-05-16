<?php

namespace App\Repository;

use App\Entity\LogsActionsIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogsActionsIntegration>
 *
 * @method LogsActionsIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogsActionsIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogsActionsIntegration[]    findAll()
 * @method LogsActionsIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogsActionsIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogsActionsIntegration::class);
    }

    public function save(LogsActionsIntegration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LogsActionsIntegration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LogsActionsIntegration[] Returns an array of LogsActionsIntegration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogsActionsIntegration
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
