<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CreancePaiementDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreancePaiementDbi>
 *
 * @method CreancePaiementDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreancePaiementDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreancePaiementDbi[]    findAll()
 * @method CreancePaiementDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreancePaiementDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreancePaiementDbi::class);
    }

    public function save(CreancePaiementDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CreancePaiementDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CreancePaiementDbi[] Returns an array of CreancePaiementDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CreancePaiementDbi
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
