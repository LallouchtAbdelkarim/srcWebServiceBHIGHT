<?php

namespace App\Repository\Customer;

use App\Entity\Customer\emploiDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<emploiDbi>
 *
 * @method emploiDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method emploiDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method emploiDbi[]    findAll()
 * @method emploiDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class emploiDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, emploiDbi::class);
    }

    public function save(emploiDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(emploiDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return emploiDbi[] Returns an array of emploiDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?emploiDbi
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
