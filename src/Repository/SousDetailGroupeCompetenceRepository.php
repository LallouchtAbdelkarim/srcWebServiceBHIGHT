<?php

namespace App\Repository;

use App\Entity\SousDetailGroupeCompetence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousDetailGroupeCompetence>
 *
 * @method SousDetailGroupeCompetence|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousDetailGroupeCompetence|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousDetailGroupeCompetence[]    findAll()
 * @method SousDetailGroupeCompetence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousDetailGroupeCompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousDetailGroupeCompetence::class);
    }

    public function save(SousDetailGroupeCompetence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SousDetailGroupeCompetence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SousDetailGroupeCompetence[] Returns an array of SousDetailGroupeCompetence objects
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

//    public function findOneBySomeField($value): ?SousDetailGroupeCompetence
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
