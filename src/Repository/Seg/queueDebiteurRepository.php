<?php

namespace App\Repository\Seg;

use App\Entity\Seg\queueDebiteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<queueDebiteur>
 *
 * @method queueDebiteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method queueDebiteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method queueDebiteur[]    findAll()
 * @method queueDebiteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class queueDebiteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, queueDebiteur::class);
    }

    public function save(queueDebiteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(queueDebiteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return queueDebiteur[] Returns an array of queueDebiteur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?queueDebiteur
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
