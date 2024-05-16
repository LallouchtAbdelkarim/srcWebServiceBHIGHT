<?php

namespace App\Repository;

use App\Entity\CadragesCreance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CadragesCreance>
 *
 * @method CadragesCreance|null find($id, $lockMode = null, $lockVersion = null)
 * @method CadragesCreance|null findOneBy(array $criteria, array $orderBy = null)
 * @method CadragesCreance[]    findAll()
 * @method CadragesCreance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CadragesCreanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CadragesCreance::class);
    }

    public function save(CadragesCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CadragesCreance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CadragesCreance[] Returns an array of CadragesCreance objects
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

//    public function findOneBySomeField($value): ?CadragesCreance
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
