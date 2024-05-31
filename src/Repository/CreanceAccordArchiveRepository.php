<?php

namespace App\Repository;

use App\Entity\CreanceAccordArchive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreanceAccordArchive>
 *
 * @method CreanceAccordArchive|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreanceAccordArchive|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreanceAccordArchive[]    findAll()
 * @method CreanceAccordArchive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreanceAccordArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreanceAccordArchive::class);
    }

    public function save(CreanceAccordArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CreanceAccordArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CreanceAccordArchive[] Returns an array of CreanceAccordArchive objects
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

//    public function findOneBySomeField($value): ?CreanceAccordArchive
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
