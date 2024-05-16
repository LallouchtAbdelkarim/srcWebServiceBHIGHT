<?php

namespace App\Repository;

use App\Entity\ReglePortefeuille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReglePortefeuille>
 *
 * @method ReglePortefeuille|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReglePortefeuille|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReglePortefeuille[]    findAll()
 * @method ReglePortefeuille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReglePortefeuilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReglePortefeuille::class);
    }

    public function save(ReglePortefeuille $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReglePortefeuille $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReglePortefeuille[] Returns an array of ReglePortefeuille objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReglePortefeuille
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
