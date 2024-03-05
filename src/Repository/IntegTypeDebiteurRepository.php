<?php

namespace App\Repository;

use App\Entity\IntegTypeDebiteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IntegTypeDebiteur>
 *
 * @method IntegTypeDebiteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntegTypeDebiteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntegTypeDebiteur[]    findAll()
 * @method IntegTypeDebiteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntegTypeDebiteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IntegTypeDebiteur::class);
    }

    public function save(IntegTypeDebiteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IntegTypeDebiteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return IntegTypeDebiteur[] Returns an array of IntegTypeDebiteur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IntegTypeDebiteur
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
