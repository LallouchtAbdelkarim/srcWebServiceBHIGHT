<?php

namespace App\Repository;

use App\Entity\ParamGroupeCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParamGroupeCritere>
 *
 * @method ParamGroupeCritere|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParamGroupeCritere|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParamGroupeCritere[]    findAll()
 * @method ParamGroupeCritere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParamGroupeCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParamGroupeCritere::class);
    }

    public function save(ParamGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ParamGroupeCritere $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ParamGroupeCritere[] Returns an array of ParamGroupeCritere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ParamGroupeCritere
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
