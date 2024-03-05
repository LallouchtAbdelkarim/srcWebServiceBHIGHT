<?php

namespace App\Repository;

use App\Entity\ScenarioObjectMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScenarioObjectMapping>
 *
 * @method ScenarioObjectMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScenarioObjectMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScenarioObjectMapping[]    findAll()
 * @method ScenarioObjectMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioObjectMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScenarioObjectMapping::class);
    }

    public function save(ScenarioObjectMapping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ScenarioObjectMapping $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ScenarioObjectMapping[] Returns an array of ScenarioObjectMapping objects
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

//    public function findOneBySomeField($value): ?ScenarioObjectMapping
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
