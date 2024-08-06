<?php

namespace App\Repository;

use App\Entity\PjDossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PjDossier>
 *
 * @method PjDossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method PjDossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method PjDossier[]    findAll()
 * @method PjDossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PjDossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PjDossier::class);
    }

//    /**
//     * @return PjDossier[] Returns an array of PjDossier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PjDossier
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
