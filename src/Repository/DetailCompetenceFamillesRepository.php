<?php

namespace App\Repository;

use App\Entity\DetailCompetenceFamilles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCompetenceFamilles>
 *
 * @method DetailCompetenceFamilles|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCompetenceFamilles|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCompetenceFamilles[]    findAll()
 * @method DetailCompetenceFamilles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCompetenceFamillesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCompetenceFamilles::class);
    }

    public function save(DetailCompetenceFamilles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailCompetenceFamilles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DetailCompetenceFamilles[] Returns an array of DetailCompetenceFamilles objects
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

//    public function findOneBySomeField($value): ?DetailCompetenceFamilles
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
