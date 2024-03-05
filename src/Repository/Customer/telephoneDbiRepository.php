<?php

namespace App\Repository\Customer;

use App\Entity\Customer\telephoneDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<telephoneDbi>
 *
 * @method telephoneDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method telephoneDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method telephoneDbi[]    findAll()
 * @method telephoneDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class telephoneDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, telephoneDbi::class);
    }

    public function save(telephoneDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(telephoneDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return telephoneDbi[] Returns an array of telephoneDbi objects
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

//    public function findOneBySomeField($value): ?telephoneDbi
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
