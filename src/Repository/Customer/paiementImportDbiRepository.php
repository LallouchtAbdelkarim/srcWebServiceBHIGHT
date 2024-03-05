<?php

namespace App\Repository\Customer;

use App\Entity\Customer\paiementImportDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<paiementImportDbi>
 *
 * @method paiementImportDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method paiementImportDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method paiementImportDbi[]    findAll()
 * @method paiementImportDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class paiementImportDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, paiementImportDbi::class);
    }

    public function save(paiementImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(paiementImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return paiementImportDbi[] Returns an array of paiementImportDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?paiementImportDbi
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
