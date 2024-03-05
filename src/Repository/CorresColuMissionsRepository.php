<?php

namespace App\Repository;

use App\Entity\CorresColuMissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CorresColuMissions>
 *
 * @method CorresColuMissions|null find($id, $lockMode = null, $lockVersion = null)
 * @method CorresColuMissions|null findOneBy(array $criteria, array $orderBy = null)
 * @method CorresColuMissions[]    findAll()
 * @method CorresColuMissions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorresColuMissionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorresColuMissions::class);
    }

    public function save(CorresColuMissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CorresColuMissions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CorresColuMissions[] Returns an array of CorresColuMissions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CorresColuMissions
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
