<?php

namespace App\Repository;

use App\Entity\DonneurOrdre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DonneurOrdre>
 *
 * @method DonneurOrdre|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonneurOrdre|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonneurOrdre[]    findAll()
 * @method DonneurOrdre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneurOrdreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonneurOrdre::class);
    }

    public function save(DonneurOrdre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DonneurOrdre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DonneurOrdre[] Returns an array of DonneurOrdre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DonneurOrdre
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
