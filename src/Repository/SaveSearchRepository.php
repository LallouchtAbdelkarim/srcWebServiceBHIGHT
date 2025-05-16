<?php

namespace App\Repository;

use App\Entity\SaveSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaveSearch>
 *
 * @method SaveSearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaveSearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaveSearch[]    findAll()
 * @method SaveSearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaveSearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaveSearch::class);
    }

//    /**
//     * @return SaveSearch[] Returns an array of SaveSearch objects
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

//    public function findOneBySomeField($value): ?SaveSearch
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
