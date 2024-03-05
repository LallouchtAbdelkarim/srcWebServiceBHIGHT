<?php

namespace App\Repository;

use App\Entity\DetailModelAffichage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailModelAffichage>
 *
 * @method DetailModelAffichage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailModelAffichage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailModelAffichage[]    findAll()
 * @method DetailModelAffichage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailModelAffichageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailModelAffichage::class);
    }

    public function save(DetailModelAffichage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailModelAffichage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailModelAffichage[] Returns an array of DetailModelAffichage objects
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

//    public function findOneBySomeField($value): ?DetailModelAffichage
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
