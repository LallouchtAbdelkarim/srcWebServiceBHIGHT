<?php

namespace App\Repository;

use App\Entity\Bookmarks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bookmarks>
 *
 * @method Bookmarks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bookmarks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bookmarks[]    findAll()
 * @method Bookmarks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookmarksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmarks::class);
    }

//    /**
//     * @return Bookmarks[] Returns an array of Bookmarks objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bookmarks
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
