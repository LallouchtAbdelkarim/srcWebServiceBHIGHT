<?php

namespace App\Repository\Customer;

use App\Entity\Customer\historiqueEmploiDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<historiqueEmploiDbi>
 *
 * @method historiqueEmploiDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method historiqueEmploiDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method historiqueEmploiDbi[]    findAll()
 * @method historiqueEmploiDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class historiqueEmploiDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, historiqueEmploiDbi::class);
    }

    public function save(historiqueEmploiDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(historiqueEmploiDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return historiqueEmploiDbi[] Returns an array of historiqueEmploiDbi objects
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

//    public function findOneBySomeField($value): ?historiqueEmploiDbi
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
