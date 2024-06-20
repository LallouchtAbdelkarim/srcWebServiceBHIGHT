<?php

namespace App\Repository;

use App\Entity\HistoriqueDemandeCadrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueDemandeCadrage>
 *
 * @method HistoriqueDemandeCadrage|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueDemandeCadrage|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueDemandeCadrage[]    findAll()
 * @method HistoriqueDemandeCadrage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueDemandeCadrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueDemandeCadrage::class);
    }

    public function save(HistoriqueDemandeCadrage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistoriqueDemandeCadrage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return HistoriqueDemandeCadrage[] Returns an array of HistoriqueDemandeCadrage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoriqueDemandeCadrage
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
