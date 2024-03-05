<?php

namespace App\Repository\Customer;

use App\Entity\Customer\typeDebiteurDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<typeDebiteurDbi>
 *
 * @method typeDebiteurDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method typeDebiteurDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method typeDebiteurDbi[]    findAll()
 * @method typeDebiteurDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class typeDebiteurDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, typeDebiteurDbi::class);
    }

    public function save(typeDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(typeDebiteurDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return typeDebiteurDbi[] Returns an array of typeDebiteurDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?typeDebiteurDbi
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
