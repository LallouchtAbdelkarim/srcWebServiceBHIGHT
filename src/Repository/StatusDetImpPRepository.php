<?php

namespace App\Repository;

use App\Entity\StatusDetImpP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusDetImpP>
 *
 * @method StatusDetImpP|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusDetImpP|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusDetImpP[]    findAll()
 * @method StatusDetImpP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusDetImpPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusDetImpP::class);
    }

    public function save(StatusDetImpP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatusDetImpP $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatusDetImpP[] Returns an array of StatusDetImpP objects
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

//    public function findOneBySomeField($value): ?StatusDetImpP
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
