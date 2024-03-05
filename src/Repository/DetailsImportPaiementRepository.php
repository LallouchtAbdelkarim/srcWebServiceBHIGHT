<?php

namespace App\Repository;

use App\Entity\DetailsImportPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsImportPaiement>
 *
 * @method DetailsImportPaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsImportPaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsImportPaiement[]    findAll()
 * @method DetailsImportPaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsImportPaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsImportPaiement::class);
    }

    public function save(DetailsImportPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsImportPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsImportPaiement[] Returns an array of DetailsImportPaiement objects
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

//    public function findOneBySomeField($value): ?DetailsImportPaiement
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
