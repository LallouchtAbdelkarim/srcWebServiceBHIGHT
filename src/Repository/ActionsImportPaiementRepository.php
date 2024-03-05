<?php

namespace App\Repository;

use App\Entity\ActionsImportPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActionsImportPaiement>
 *
 * @method ActionsImportPaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionsImportPaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionsImportPaiement[]    findAll()
 * @method ActionsImportPaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionsImportPaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionsImportPaiement::class);
    }

    public function save(ActionsImportPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ActionsImportPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ActionsImportPaiement[] Returns an array of ActionsImportPaiement objects
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

//    public function findOneBySomeField($value): ?ActionsImportPaiement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
