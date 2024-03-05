<?php

namespace App\Repository;

use App\Entity\DetailsSendCommunication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsSendCommunication>
 *
 * @method DetailsSendCommunication|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsSendCommunication|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsSendCommunication[]    findAll()
 * @method DetailsSendCommunication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsSendCommunicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsSendCommunication::class);
    }

    public function save(DetailsSendCommunication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsSendCommunication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsSendCommunication[] Returns an array of DetailsSendCommunication objects
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

//    public function findOneBySomeField($value): ?DetailsSendCommunication
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
