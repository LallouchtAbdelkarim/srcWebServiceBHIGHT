<?php

namespace App\Repository\Customer;

use App\Entity\Customer\DetailsAccordPaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsAccordPaiement>
 *
 * @method DetailsAccordPaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsAccordPaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsAccordPaiement[]    findAll()
 * @method DetailsAccordPaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsAccordPaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsAccordPaiement::class);
    }

    public function save(DetailsAccordPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailsAccordPaiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailsAccordPaiement[] Returns an array of DetailsAccordPaiement objects
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

//    public function findOneBySomeField($value): ?DetailsAccordPaiement
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
