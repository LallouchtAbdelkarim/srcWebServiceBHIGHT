<?php

namespace App\Repository;

use App\Entity\IntermParamEtap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntermParamEtap>
 *
 * @method IntermParamEtap|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntermParamEtap|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntermParamEtap[]    findAll()
 * @method IntermParamEtap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntermParamEtapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntermParamEtap::class);
    }

    public function save(IntermParamEtap $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntermParamEtap $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntermParamEtap[] Returns an array of IntermParamEtap objects
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

//    public function findOneBySomeField($value): ?IntermParamEtap
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
