<?php

namespace App\Repository\Customer;

use App\Entity\Customer\creanceAccord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<creanceAccord>
 *
 * @method creanceAccord|null find($id, $lockMode = null, $lockVersion = null)
 * @method creanceAccord|null findOneBy(array $criteria, array $orderBy = null)
 * @method creanceAccord[]    findAll()
 * @method creanceAccord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class creanceAccordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, creanceAccord::class);
    }

    public function save(creanceAccord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(creanceAccord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return creanceAccord[] Returns an array of creanceAccord objects
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

//    public function findOneBySomeField($value): ?creanceAccord
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
