<?php

namespace App\Repository;

use App\Entity\StatusDetailsAccord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusDetailsAccord>
 *
 * @method StatusDetailsAccord|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusDetailsAccord|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusDetailsAccord[]    findAll()
 * @method StatusDetailsAccord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusDetailsAccordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusDetailsAccord::class);
    }

    public function save(StatusDetailsAccord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatusDetailsAccord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatusDetailsAccord[] Returns an array of StatusDetailsAccord objects
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

//    public function findOneBySomeField($value): ?StatusDetailsAccord
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
