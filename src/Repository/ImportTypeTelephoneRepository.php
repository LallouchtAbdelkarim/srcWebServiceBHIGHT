<?php

namespace App\Repository;

use App\Entity\ImportTypeTelephone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportTypeTelephone>
 *
 * @method ImportTypeTelephone|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportTypeTelephone|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportTypeTelephone[]    findAll()
 * @method ImportTypeTelephone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportTypeTelephoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportTypeTelephone::class);
    }

    public function save(ImportTypeTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImportTypeTelephone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImportTypeTelephone[] Returns an array of ImportTypeTelephone objects
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

//    public function findOneBySomeField($value): ?ImportTypeTelephone
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
