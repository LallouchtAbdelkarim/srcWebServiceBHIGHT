<?php

namespace App\Repository;

use App\Entity\DetailCreanceArchive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCreanceArchive>
 *
 * @method DetailCreanceArchive|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCreanceArchive|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCreanceArchive[]    findAll()
 * @method DetailCreanceArchive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCreanceArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCreanceArchive::class);
    }

    public function save(DetailCreanceArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailCreanceArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailCreanceArchive[] Returns an array of DetailCreanceArchive objects
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

//    public function findOneBySomeField($value): ?DetailCreanceArchive
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
