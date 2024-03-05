<?php

namespace App\Repository;

use App\Entity\IntermParamSousEtap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntermParamSousEtap>
 *
 * @method IntermParamSousEtap|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntermParamSousEtap|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntermParamSousEtap[]    findAll()
 * @method IntermParamSousEtap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntermParamSousEtapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntermParamSousEtap::class);
    }

    public function save(IntermParamSousEtap $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntermParamSousEtap $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntermParamSousEtap[] Returns an array of IntermParamSousEtap objects
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

//    public function findOneBySomeField($value): ?IntermParamSousEtap
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
