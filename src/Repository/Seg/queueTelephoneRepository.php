<?php

namespace App\Repository\Seg;

use App\Entity\Seg\queueTelephone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<queueTelephone>
 *
 * @method queueTelephone|null find($id, $lockMode = null, $lockVersion = null)
 * @method queueTelephone|null findOneBy(array $criteria, array $orderBy = null)
 * @method queueTelephone[]    findAll()
 * @method queueTelephone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class queueTelephoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, queueTelephone::class);
    }

    public function save(queueTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(queueTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return queueTelephone[] Returns an array of queueTelephone objects
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

//    public function findOneBySomeField($value): ?queueTelephone
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
