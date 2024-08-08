<?php

namespace App\Repository;

use App\Entity\StatusDossierAssign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusDossierAssign>
 *
 * @method StatusDossierAssign|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusDossierAssign|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusDossierAssign[]    findAll()
 * @method StatusDossierAssign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusDossierAssignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusDossierAssign::class);
    }

//    /**
//     * @return StatusDossierAssign[] Returns an array of StatusDossierAssign objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatusDossierAssign
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
