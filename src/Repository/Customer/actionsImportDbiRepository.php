<?php

namespace App\Repository\Customer;

use App\Entity\Customer\actionsImportDbi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<actionsImportDbi>
 *
 * @method actionsImportDbi|null find($id, $lockMode = null, $lockVersion = null)
 * @method actionsImportDbi|null findOneBy(array $criteria, array $orderBy = null)
 * @method actionsImportDbi[]    findAll()
 * @method actionsImportDbi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class actionsImportDbiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, actionsImportDbi::class);
    }

    public function save(actionsImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(actionsImportDbi $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return actionsImportDbi[] Returns an array of actionsImportDbi objects
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

//    public function findOneBySomeField($value): ?actionsImportDbi
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
