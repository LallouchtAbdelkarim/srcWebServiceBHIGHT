<?php

namespace App\Repository;

use App\Entity\ImportDonneurOrdreBack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportDonneurOrdreBack>
 *
 * @method ImportDonneurOrdreBack|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportDonneurOrdreBack|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportDonneurOrdreBack[]    findAll()
 * @method ImportDonneurOrdreBack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportDonneurOrdreBackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportDonneurOrdreBack::class);
    }

    public function save(ImportDonneurOrdreBack $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImportDonneurOrdreBack $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImportDonneurOrdreBack[] Returns an array of ImportDonneurOrdreBack objects
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

//    public function findOneBySomeField($value): ?ImportDonneurOrdreBack
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
