<?php

namespace App\Repository;

use App\Entity\IntermGroupeCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntermGroupeCritere>
 *
 * @method IntermGroupeCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntermGroupeCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntermGroupeCritere[]    findAll()
 * @method IntermGroupeCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntermGroupeCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntermGroupeCritere::class);
    }

    public function save(IntermGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntermGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntermGroupeCritere[] Returns an array of IntermGroupeCritere objects
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

//    public function findOneBySomeField($value): ?IntermGroupeCritere
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
