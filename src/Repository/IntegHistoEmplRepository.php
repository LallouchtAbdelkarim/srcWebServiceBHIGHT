<?php

namespace App\Repository;

use App\Entity\IntegHistoEmpl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegHistoEmpl>
 *
 * @method IntegHistoEmpl|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntegHistoEmpl|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntegHistoEmpl[]    findAll()
 * @method IntegHistoEmpl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntegHistoEmplRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegHistoEmpl::class);
    }

    public function save(IntegHistoEmpl $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntegHistoEmpl $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntegHistoEmpl[] Returns an array of IntegHistoEmpl objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IntegHistoEmpl
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
