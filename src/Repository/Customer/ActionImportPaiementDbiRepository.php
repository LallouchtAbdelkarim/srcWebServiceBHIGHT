<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ActionImportPaiementDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActionImportPaiementDbi>
 *
 * @method ActionImportPaiementDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionImportPaiementDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionImportPaiementDbi[]    findAll()
 * @method ActionImportPaiementDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionImportPaiementDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionImportPaiementDbi::class);
    }

    public function save(ActionImportPaiementDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ActionImportPaiementDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ActionImportPaiementDbi[] Returns an array of ActionImportPaiementDbi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActionImportPaiementDbi
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
